<?php

namespace Eccube\Di;

class ProviderGenerator
{
    protected $template = '<?php

namespace Eccube\ServiceProvider;

class ServiceProviderCache implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $app)
    {
        {% for component in components -%}
        $container["{{ component.id }}"] = function (\Pimple\Container $app) {
            $class = new \ReflectionClass(\{{ component.class_name }}::class);
            $instance = $class->newInstanceWithoutConstructor();
            {% for inject in component.injects -%}
            $property = $class->getProperty("{{ inject.property_name }}");
            $property->setAccessible(true);
            $property->setValue($instance, $app["{{ inject.id }}"]);
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
