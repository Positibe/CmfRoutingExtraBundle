PositibeOrmRoutingBundle
========================

The PositibeOrmRoutingBundle add Doctrine ORM support for Symfony CmfRoutingBundle to store routing on orm databses and support auto routing system.

Installation
------------

To install the bundle just add the dependent bundles:

    php composer.phar require positibe/orm-routing-bundle

Next, be sure to enable the bundles in your application kernel:

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

Configuration
-------------

Import all necessary configurations to your app/config/config.yml the basic configuration.
    # app/config/config.yml
    imports:
        - { resource: @PositibeOrmRoutingBundle/Resources/config/config.yml }

**Caution:**: This bundle use the timestampable, sluggable, translatable and sortable extension of GedmoDoctrineExtension. Be sure that you have the listeners for this extensions enable. You can also to use StofDoctrineExtensionBundle.

Remember to update the schema:

    php app/console doctrine:schema:update --force

Using
-----

An entity that has routes must implement `Symfony\Cmf\Component\Routing\RouteReferrersInterface`.

Add to any entity you want the relation with `Positibe\Bundle\OrmRoutingBundle\Entity\Route`:

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

**Tip:** You can use `Positibe\Bundle\OrmRoutingBundle\Entity\HasRoutesTrait` to simplify the implementation of RouteReferrerInterface methods and mapping. This create a many to many relation without doing nothing more.

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

Entity Repositories
-------------------

**Important**: The Repository for your entity must implement `Positibe\Bundle\OrmRoutingBundle\Entity\HasRepositoryInterface`.

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

**Tip:** You can use `Positibe\Bundle\OrmRoutingBundle\Entity\HasRoutesRepositoryTrait` to simplify the implementation of HasRoutesRepositoryInterface methods.

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

Creating routes
---------------

    $post = new Post(); //Class that implement `Symfony\Cmf\Component\Routing\RouteReferrersInterface`
    $post->setTitle('You're awesome'); //Fill datas

    $route = new Route(); //Class of `Positibe\Bundle\OrmRoutingBundle\Entity\Route`
    $route->setStaticPrefix('you-are-awesome'); //Set the permalink of post instance
    $post->addRoute($route);

    $em->persist($post);
    $em->flush();

Creating automatic routes
-------------------------

See on `auto_routing.md`.

For more information see the [Symfony Cmf Routing Bundle Documentation](http://symfony.com/doc/master/cmf/bundles/routing/index.html)
