PositibeCmfRoutingExtraBundle
=============================

El PositibeCmfRoutingExtraBundle agrega soporte del Doctrine ORM a Symfony CmfRoutingBundle para almacenar las rutas en bases de datos orm.

Instalación
-----------

Para instalar el bundle solo agrega la dependencia del bundle:

    php composer.phar require positibe/cmf-routing-extra-bundle

Después, asegurate de habilitar los bundles instalados en el kernel de aplicaciones:

    <?php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Positibe\Bundle\CmfRoutingExtraBundle\PositibeCmfRoutingExtraBundle(),

            // ...
        );
    }

Configuración
-------------

Las configuraciones básicas de los bundles instalados pueden ser importadas en tus configuaciones en app/config/config.yml:
    # app/config/config.yml
    imports:
        - { resource: @PositibeCmfRoutingExtraBundle/Resources/config/config.yml }

**Precaución:**: Este bundle usa las extensiones timestampable, sluggable, translatable and sortable de GedmoDoctrineExtension. Asegúrate de tener los listeners para estas extensiones. Puede usar StofDoctrineExtensionBundle para esto.

Remember to update the schema:

    php app/console doctrine:schema:update --force

Usando
------

Usa entidad que posee rutas debe implementar `Symfony\Cmf\Component\Routing\RouteReferrersInterface`.

Agrega la relación con `Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute`:

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
         * @ORM\ManyToMany(targetEntity="Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute", orphanRemoval=TRUE, cascade="all")
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

**Pista:** Puedes usar el trait `Positibe\Bundle\CmfRoutingExtraBundle\Entity\HasRoutesTrait` para simplificar la implementación de los métodos RouteReferrerInterface y el mapeo. Esto creará una relación mucho a mucho sin hacer nada más.

    <?php
    // src/AppBundle/Entity/Post.php
    namespace AppBundle\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Positibe\Bundle\CmfRoutingExtraBundle\Entity\HasRoutesTrait;
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

Creando rutas
-------------

    $post = new Post(); //Class that implement `Symfony\Cmf\Component\Routing\RouteReferrersInterface`
    $post->setTitle('You're awesome'); //Fill datas
    $manager->persist($post);
    $manager->flush(); //Flush to be able to take the id of the `$post`

    $contentRepository = $this->container->get('cmf_routing.content_repository');
    $route = new AutoRoute(); //Class of `Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute`
    $route->setStaticPrefix('/you-are-awesome'); //Set the permalink of post instance
    $route->setDefault(RouteObjectInterface::CONTENT_ID, $contentRepository->getContentId($post)); this set ``FQN:id`` into ``content_id``
    $route->setContent($post);
    $post->addRoute($route);

    $em->persist($post);
    $em->flush();

Creando rutas automáticas
-------------------------

Ver en `auto_routing.md`.

Para mayor información ver la documentación oficial de [Symfony Cmf Routing Bundle Documentation](http://symfony.com/doc/master/cmf/bundles/routing/index.html)