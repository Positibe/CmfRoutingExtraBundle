PositibeOrmRoutingBundle
========================

El PositibeOrmRoutingBundle agrega soporte del Doctrine ORM a Symfony CmfRoutingBundle para almacenar las rutas en bases de datos orm.

Instalación
-----------

Para instalar el bundle solo agrega la dependencia del bundle:

    php composer.phar require positibe/orm-routing-bundle

Después, asegurate de habilitar los bundles instalados en el kernel de aplicaciones:

    <?php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Positibe\Bundle\OrmRoutingBundle\PositibeOrmRoutingBundle(),

            // ...
        );
    }

Configuración
-------------

Las configuraciones básicas de los bundles instalados pueden ser importadas en tus configuaciones en app/config/config.yml:
    # app/config/config.yml
    imports:
        - { resource: @PositibeOrmRoutingBundle/Resources/config/config.yml }

**Precaución:**: Este bundle usa las extensiones timestampable, sluggable, translatable and sortable de GedmoDoctrineExtension. Asegúrate de tener los listeners para estas extensiones. Puede usar StofDoctrineExtensionBundle para esto.

Remember to update the schema:

    php app/console doctrine:schema:update --force

Usando
------

Usa entidad que posee rutas debe implementar `Symfony\Cmf\Component\Routing\RouteReferrersInterface`.

Agrega la relación con `Positibe\Bundle\OrmRoutingBundle\Entity\Route`:

    <?php
    // src/AppBundle/Entity/Post.php
    namespace AppBundle\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Symfony\Cmf\Component\Routing\RouteObjectInterface;
    use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
    use Doctrine\ORM\Mapping as ORM;

    /**
     *
     * @ORM\Table(name="app_post")
     * @ORM\Entity(repositoryClass="AppBundle\Entity\PostRepository")
     */
    class Post implements RouteReferrersInterface {

        /**
         * @var ArrayCollection|RouteObjectInterface[]
         *
         * @ORM\ManyToMany(targetEntity="Positibe\Bundle\OrmRoutingBundle\Entity\Route", orphanRemoval=TRUE, cascade="all")
         * @ORM\JoinTable(name="app_post_routes")
         */
        protected $routes;

        public function __construct()
        {
            $this->routes = new ArrayCollection();
        }

        /**
         * @return ArrayCollection|\Symfony\Cmf\Component\Routing\RouteObjectInterface[]
         */
        public function getRoutes()
        {
            return $this->routes;
        }

        /**
         * @param ArrayCollection|\Symfony\Cmf\Component\Routing\RouteObjectInterface[] $routes
         */
        public function setRoutes($routes)
        {
            $this->routes = $routes;
        }

        /**
         * Add a route to the collection.
         *
         * @param \Symfony\Component\Routing\Route $route
         * @return $this
         */
        public function addRoute($route)
        {
            $this->routes[] = $route;

            return $this;
        }

        /**
         * Remove a route from the collection.
         *
         * @param \Symfony\Component\Routing\Route $route
         */
        public function removeRoute($route)
        {
            $this->routes->removeElement($route);
        }
    }

**Pista:** Puedes usar el trait `Positibe\Bundle\OrmRoutingBundle\Entity\HasRoutesTrait` para simplificar la implementación de los métodos RouteReferrerInterface y el mapeo. Esto creará una relación mucho a mucho sin hacer nada más.

    <?php
    // src/AppBundle/Entity/Post.php
    namespace AppBundle\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Positibe\Bundle\OrmRoutingBundle\Entity\HasRoutesTrait;
    use Doctrine\ORM\Mapping as ORM;

    /**
     *
     * @ORM\Table(name="app_post")
     * @ORM\Entity(repositoryClass="AppBundle\Entity\PostRepository")
     */
    class Post implements RouteReferrersInterface {

        use HasRoutesTrait;

        public function __construct()
        {
            $this->routes = new ArrayCollection();
        }
    }

Repositios de entidad
---------------------

**Important**: El repositorio para tu entidad debe implementar `Positibe\Bundle\OrmRoutingBundle\Entity\HasRepositoryInterface`. Con esto debe implementar el método findByRoute debido a que la relación de mucho a mucho no se puede obtener sin realizar una consulta dentro del repositorio

    <?php
    // src/AppBundle/Entity/PostRepository.php
    namespace AppBundle\Entity;

    use Doctrine\ORM\EntityRepository;
    use Positibe\Bundle\OrmRoutingBundle\Entity\HasRoutesRepositoryInterface;

    class PostRepository extends EntityRepository implements HasRoutesRepositoryInterface
    {
        /**
         * @param $route
         * @return mixed
         * @throws \Doctrine\ORM\NonUniqueResultException
         */
        public function findByRoute($route)
        {
            $qb = $this->createQueryBuilder('c')
                ->join('c.routes', 'r')
                ->where('r = :route')
                ->setParameter('route', $route);

            return $qb->getQuery()->getOneOrNullResult();
        }
    }

**Pista:** Puedes usar el `Positibe\Bundle\OrmRoutingBundle\Entity\HasRoutesRepositoryTrait` para simplificar la implementación del métodos de HasRoutesRepositoryInterface.

    <?php
    // src/AppBundle/Entity/PostRepository.php
    namespace AppBundle\Entity;

    use Doctrine\ORM\EntityRepository;
    use Positibe\Bundle\OrmRoutingBundle\Entity\HasRoutesRepositoryInterface;
    use Positibe\Bundle\OrmRoutingBundle\Entity\HasRoutesRepositoryTrait;

    class PostRepository extends EntityRepository implements HasRoutesRepositoryInterface
    {
        use HasRoutesRepositoryTrait;
    }

Creando rutas
-------------

    $post = new Post(); //Class that implement `Symfony\Cmf\Component\Routing\RouteReferrersInterface`
    $post->setTitle('You're awesome'); //Fill datas

    $route = new Route(); //Class of `Positibe\Bundle\OrmRoutingBundle\Entity\Route`
    $route->setStaticPrefix('you-are-awesome'); //Set the permalink of post instance
    $post->addRoute($route);

    $em->persist($post);
    $em->flush();

Creando rutas automáticas
-------------------------

Ver en `auto_routing.md`.



Para mayor información ver la documentación oficial de [Symfony Cmf Routing Bundle Documentation](http://symfony.com/doc/master/cmf/bundles/routing/index.html)