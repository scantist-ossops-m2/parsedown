<?php

namespace Erusev\Parsedown\Components\Inlines;

use Erusev\Parsedown\AST\StateRenderable;
use Erusev\Parsedown\Components\Inline;
use Erusev\Parsedown\Html\Renderables\Element;
use Erusev\Parsedown\Html\Renderables\Text;
use Erusev\Parsedown\Parsedown;
use Erusev\Parsedown\Parsing\Excerpt;
use Erusev\Parsedown\State;

final class Url implements Inline
{
    use WidthTrait;

    /** @var string */
    private $url;

    /** @var int */
    private $position;

    /**
     * @param string $url
     * @param int $position
     */
    public function __construct($url, $position)
    {
        $this->url = $url;
        $this->width = \strlen($url);
        $this->position = $position;
    }

    /**
     * @param Excerpt $Excerpt
     * @param State $State
     * @return static|null
     */
    public static function build(Excerpt $Excerpt, State $State)
    {
        $text = $Excerpt->text();

        if (\strlen($text) < 2 or \substr($text, 2, 1) !== '/') {
            return null;
        }

        if (\strpos($Excerpt->context(), 'http') !== false
            and \preg_match('/\bhttps?+:[\/]{2}[^\s<]+\b\/*+/ui', $Excerpt->context(), $matches, \PREG_OFFSET_CAPTURE)
        ) {
            return new self($matches[0][0], \intval($matches[0][1]));
        }

        return null;
    }

    /**
     * Return an integer to declare that the inline should be treated as if it
     * started from that position in the excerpt given to static::build.
     * Return null to use the excerpt offset value.
     * @return int|null
     * */
    public function modifyStartPositionTo()
    {
        return $this->position;
    }

    /**
     * @return Element
     */
    public function stateRenderable(Parsedown $_)
    {
        return new Element('a', ['href' => $this->url], [new Text($this->url)]);
    }

    /**
     * @return Text
     */
    public function bestPlaintext()
    {
        return new Text($this->url);
    }
}