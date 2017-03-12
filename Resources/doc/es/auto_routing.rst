Auto Routing
============

Puedes configurar la creación de las rutas de una forma sencilla similar a Symfony CmfRoutingAutoBundle.

Usando la misma estructura genera de forma automáticas las nutas para tus contenidos.

Solo debes configurar bajo `positibe_routing.auto_routing` usando la estructura habilitada.

    # app/config/config.yml
    positibe_cmf_routing_extra:
        auto_routing:
            AppBundle\Entity\BlogPost:
                uri_schema: /{blog}/{title}
                token_providers:
                    blog: [content_method, { method: getBlogTitle }]
                    title: [content_method, { method: getTitle }]

Esto permite crear una ruta con la estructura /{blog}/{title}, donde `blog` será un valor que obtenga del método getBlogTitle() de la clase y `title` del método getTitle() del enitdad.

Digamos que tengamos una instancia de la clase con title: `Auto Routing` que está dentro de la categoría: `Symfony`. La ruta resultante sería: `/symfony/auto-routing`

Para poder decirle a doctrine cual es la entidad que desea agregarla con auto-routing debemos agregar el listener a nuestra entidad.

    <?php
    // src/AppBundle/Entity/Post.php
    namespace AppBundle\Entity;

    //...

    /**
     * @ORM\Table(name="app_post")
     * @ORM\Entity(repositoryClass="AppBundle\Entity\PostRepository")
     *
     * ***** Haciendo esto aquí optimizamos el rendimiento de los eventos en Doctrine
     * @ORM\EntityListeners({"Positibe\Bundle\CmfRoutingExtraBundle\EventListener\RoutingAutoEntityListener"})
     * *****
     */
    class Post implements RouteReferrersInterface {
        //..
    }

Controlador por clase
---------------------

Ahora debemos decirle que controlador es que el va a manejar el contenido encontrado.

Lo que hace la funcionalidad es llamar a un controllado similar a como lo hace normalmente Symfony y solo va a agregar a los attributos de la petición el contenido relacionado con la ruta.

La configuración se realiza :

    # app/config/config.yml
    cmf_routing:
        dynamic:
            controllers_by_class:
                AppBundle\Entity\BlogPost: AppBundle:Default:index

El controlador opcionalmente puede recibir el parámetro $contentDocument y $contentTemplate

    <?php
    // src/AppBundle/Controller/DefaultController.php
    namespace AppBundle\Controller

    class DefaultController
    {
        private $defaultTemplate = 'index.html.twig';

        public function indexAction(Request $request, $contentDocument, $contentTemplate = null)
        {
            $contentTemplate = $contentTemplate ?: $this->defaultTemplate;

            $contentTemplate = str_replace(
                array('{_format}', '{_locale}'),
                array($request->getRequestFormat(), $request->getLocale()),
                $contentTemplate
            );

            $params = array('content' => $contentDocument);

            return $this->render($contentTemplate, $params);
        }
    }

Plantilla por clase
-------------------

Para definir que plantilla se usará para cada clase y no la usada por defecto podemos hacerlo similar al anterior, en este caso si no se provee de un controlador usará uno genérico, este devuelve el contenido en la variable `content`:

    # app/config/config.yml
    cmf_routing:
        dynamic:
            templates_by_class:
                AppBundle\Entity\BlogPost: AppBundle:Default:index.html.twig

Contenidos personalizados
-------------------------

Si su contenido implementa la interfaz `Positibe\Bundle\CmfRoutingExtraBundle\Model\CustomRouteInterface`, se puede auto generar las rutas basados en los datos de los métodos que implementa esta clase.

Para esto puede crear una configuración de los controladores que posee en su sistema:

    # app/config/config.yml

    positibe_cmf_routing_extra:
        controllers:
            homepage:
                _controller: [FrameworkBundle:Template:template, {template: "index.html.twig"}]
            default:
                _controller: [AppBundle:Default:index, {}]

Puede acceder a la lista de controladores mediante el servicio `positibe_routing.route_factory`.
