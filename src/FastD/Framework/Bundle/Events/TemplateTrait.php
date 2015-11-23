<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/6/30
 * Time: 下午2:55
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Events;

use FastD\Template\Template;

/**
 * Class TemplateEvent
 *
 * @package FastD\Framework\Events
 */
trait TemplateTrait
{
    /**
     * @var Template
     */
    protected $template;

    /**
     * render template show page.
     *
     * @param string $template template content string or template file path.
     * @param array $parameters
     * @return string
     */
    public function render($template, array $parameters = array())
    {
        if (null === $this->template) {
            $paths = $this->getParameters('template.paths');
            foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
                $paths[] = dirname($bundle->getRootPath());
            }
            $options = [];
            if (!($isDebug = $this->container->get('kernel')->isDebug())) {
                $options = [
                    'cache' => $this->getParameters('template.cache'),
                    'debug' => $isDebug,
                ];
            }
            $self = $this;
            $this->template = $this->container->get('kernel.template', [$paths, $options]);
            $this->template->addGlobal('request', $this->getRequest());
            $this->template->addFunction(new \Twig_SimpleFunction('url', function ($name, array $parameters = [], $format = '') use ($self) {
                return $self->generateUrl($name, $parameters, $format);
            }));
            $this->template->addFunction(new \Twig_SimpleFunction('asset', function ($name, $host = null, $path = null) use ($self) {
                return $self->asset($name, $host, $path);
            }));
            unset($paths, $options);
        }

        return $this->template->render($template, $parameters);
    }
}