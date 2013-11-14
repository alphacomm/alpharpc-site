<?php
namespace AlphaRPC\Bundle\SiteBundle;

use Sculpin\Bundle\MarkdownBundle\SculpinMarkdownBundle;
use Sculpin\Bundle\TwigBundle\SculpinTwigBundle;
use Sculpin\Core\Event\ConvertEvent;
use Sculpin\Core\Sculpin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConvertListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Sculpin::EVENT_BEFORE_CONVERT => 'beforeConvert',
            Sculpin::EVENT_AFTER_CONVERT => 'afterConvert',
        );
    }

    public function beforeConvert(ConvertEvent $convertEvent)
    {
        if (!$convertEvent->isHandledBy(SculpinMarkdownBundle::CONVERTER_NAME, SculpinTwigBundle::FORMATTER_NAME)) {
            return;
        }
        $content = $convertEvent->source()->content();
        $content = str_replace('```', '~~~', $content);
        $convertEvent->source()->setContent($content);
    }

    /**
     * Called after conversion
     *
     * @param ConvertEvent $convertEvent Convert event
     */
    public function afterConvert(ConvertEvent $convertEvent)
    {
        if (!$convertEvent->isHandledBy(SculpinMarkdownBundle::CONVERTER_NAME, SculpinTwigBundle::FORMATTER_NAME)) {
            return;
        }
        $content = $convertEvent->source()->content();

        $matches = array();
        preg_match_all('#<(h[0-6])([^>]*)>(.*)</\1>#Us', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $id = static::createIdFromLabel($match[3]);
            $attribs = self::injectId($id, $match[2]);
            $content = str_replace(
                $match[0],
                '<'.$match[1].$attribs.'>'.$match[3].'</'.$match[1].'>',
                $content
            );
        }
        $convertEvent->source()->setContent($content);
    }

    protected static function injectId($id, $attribString)
    {
        $split = preg_split('/\s+?/', trim($attribString));
        $attribs = array();
        foreach ($split as $attrib) {
            if (substr($attrib, 0, 3) == 'id=') {
                continue;
            }
            $attribs[] = $attrib;
        }

        $attribs[] = 'id="'.$id.'"';
        return ' '.implode(' ', $attribs);
    }

    protected static function createIdFromLabel($label)
    {
        return preg_replace('#[^A-Za-z0-9]+?#', '-', $label);
    }
}
