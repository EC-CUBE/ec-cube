<?php

namespace Eccube\Di;

use Eccube\Annotation\Repository;
use Eccube\Application;

class ProviderGenerator
{
    protected $template = '<?php

namespace Eccube\ServiceProvider;

class ServiceProviderCache implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $app)
    {
        {% for component in components -%}
        $app["{{ component.id }}"] = function (\Pimple\Container $app) {
            $class = new \ReflectionClass(\{{ component.class_name }}::class);
            {% if is_repository(component.anno) -%}
            $instance = $app["orm.em"]->getRepository(\{{ component.class_name }}::class);
            {%- else -%}
            $instance = $class->newInstanceWithoutConstructor();
            {%- endif %}
            {% for inject in component.injects -%}
            $property = $class->getProperty("{{ inject.property_name }}");
            $property->setAccessible(true);
            $property->setValue($instance, {% if is_app(inject.id) %}$app{% else %}$app["{{ inject.id }}"]{% endif %});
            {%- endfor %}

            return $instance;
        };
        {%- endfor %}

        {% if form_types|length > 0 %}
        $app->extend("form.types", function ($types) {
            {% for types in form_types %}
            $types[] = "{{ types.id }}";
            {% endfor %}
            return $types;
        });
        {% endif %}

        {% if form_extensions|length > 0 %}
        $app->extend("form.type.extensions", function ($extensions) {
            {% for extension in form_extensions %}
            $extensions[] = "{{ extension.id }}";
            {% endfor %}
            return $extensions;
        });
        {% endif %}
    }
}';

    public function generate($components)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array());
        $twig->addFunction(new \Twig_SimpleFunction('is_repository', function ($anno) {
            return $anno instanceof Repository;
        }));
        $twig->addFunction(new \Twig_SimpleFunction('is_app', function ($class) {
            error_log($class);
            return $class === Application::class;
        }));
        $template = $twig->createTemplate($this->template);

        return $template->render(
            [
                'components' => $components,
                'form_types' => [],
                'form_extensions' => [],
            ]
        );
    }
}
