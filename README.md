PositibeCmfRoutingExtraBundle
=============================

The PositibeCmfRoutingExtraBundle add Doctrine ORM support for Symfony CmfRoutingBundle to store routing on orm databses

Installation
------------

To install the bundle just add the dependent bundles:

    php composer.phar require positibe/cmf-routing-extra-bundle

Next, be sure to enable the bundles in your application kernel:

    <?php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Symfony\Cmf\Bundle\RoutingAutoBundle\CmfRoutingAutoBundle(),
            new Positibe\Bundle\CmfRoutingExtraBundle\PositibeCmfRoutingExtraBundle(),

            // ...
        );
    }

Configuration
-------------

Import all necessary configurations to your app/config/config.yml the basic configuration.
    # app/config/config.yml
    imports:
        - { resource: @PositibeCmfRoutingExtraBundle/Resources/config/config.yml }

**Caution:**: This bundle use the timestampable, sluggable, translatable and sortable extension of GedmoDoctrineExtension. Be sure that you have the listeners for this extensions enable. You can also to use StofDoctrineExtensionBundle.

Remember to update the schema:

    php app/console doctrine:schema:update --force

Using
-----

An entity that has routes must implement `Symfony\Cmf\Component\Routing\RouteReferrersInterface`.

Add to any entity you want the relation with `Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute` and the needed methods:

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

**Tip:** You can use `Positibe\Bundle\CmfRoutingExtraBundle\Entity\HasRoutesTrait` to simplify the implementation of RouteReferrerInterface methods and mapping. This create a many to many relation without doing nothing more.

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

Creating routes
---------------

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

Content with Custom Routing
---------------------------

If your content implement `Positibe\Bundle\CmfRoutingExtraBundle\Model\CustomRouteInterface`, you can update all your
routes with the selected controller, without the need of do it on each one.

    [yaml]
    # app/config/config.yml
    positibe_cmf_routing_extra:
        controllers:
            homepage:
                _controller: [FrameworkBundle:Template:template, {template: "index.html.twig"}]
            default:
                _controller: [AppBundle:Default:index, {}]

You have the access to this config through `positibe_cmf_routing_extra.route_factory`.

Creating automatic routes
-------------------------

See on `auto_routing.md`.

For more information see the [Symfony Cmf Routing Bundle Documentation](http://symfony.com/doc/master/cmf/bundles/routing/index.html)