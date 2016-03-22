Formulario simple de rutas
==========================

Introducción
------------

Las rutas pueden tener desde el punto de vista de programación gran cantidad de opciones, pero en la mayoría de las ocaciones, desde e punto de vista de un editor solo necesita definir el link permanente para un contenido.

El link permanente es la dirección url única y fija que posee un contenido, de forma que pueda ser referido o referenciado desde cualquier punto de la web. Si tenemos una blog "symfony-awesome.com" con un artículo que se llame "Symfony es increible" el link permanente puede ser http://symfony-awesome.com/symfony-es-increible .

FormType
--------

El bundle posee un tipo de formulario para que cree la ruta basado en el link permanente solamente.

El tipo de formulario es capas de buscar entre todas las rutas de la entidad la que necesita actualizar basado en el idioma. Si posee dos rutas con el mismo idioma para una entidad, se utilizará por defecto la primera que se encuentre.

**Tip:** No debes crear dos rutas indexables en un mismo idioma para una mismo contenido pues es seriamente penalizado por los buscadores.

Usando el positibe_route_permalink
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Un tipo de formulario de entidad que posea rutas solo debe defirnir el tipo del campo como `positibe_route_permalink` y pasarle en las opciones `'content_has_routes' => $options['data']` para poder acceder a los datos del formulario:

    <?php
    // src/AppBundle/Form/PostType.php
    namespace AppBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class PostType extends AbstractType
    {
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('title')
                ->add('description')
                ->add('routes', 'positibe_route_permalink', array(
                        'content_has_routes' => $options['data']
                    ))
            ;
        }

        /**
         * @param OptionsResolverInterface $resolver
         */
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Post'
            ));
        }

        /**
         * @return string
         */
        public function getName()
        {
            return 'post';
        }
    }

Si está mostrando el formulario en varios idiomas, deberías pasarle el idioma actual en la opción `current_locale`:

    <?php
    // src/AppBundle/Form/PostType.php
    namespace AppBundle\Form;

    //..

    class PostType extends AbstractType
    {
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('title')
                ->add('description')
                ->add('routes', 'positibe_route_permalink', array(
                        'content_has_routes' => $options['data'],
                        'current_locale' => $options['data']->getLocale() // O cualquier vía que tenga para pasarle el locale
                    ))
            ;
        }
        //..
    }