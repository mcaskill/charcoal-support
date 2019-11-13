<?php

namespace Charcoal\Support\Cms\Object;

// From 'charcoal-object'
use Charcoal\Object\Content;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Cms\Metatag\HasMetatagTrait;
use Charcoal\Support\Cms\Metatag\HasOpenGraphTrait;
use Charcoal\Support\Cms\Metatag\HasTwitterCardTrait;
use Charcoal\Support\Cms\Object\WebContentInterface;

/**
 * Hypertext Content Model
 */
abstract class AbstractWebContent extends Content implements
    WebContentInterface
{
    use HasMetatagTrait;
    use HasOpenGraphTrait;
    use HasTwitterCardTrait;

    /**
     * Objects are not locked by default.
     *
     * @var boolean
     */
    private $locked = false;

    /**
     * Alias of {@see Content::getActive()}.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Alias of {@see self::getLocked()}.
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->getLocked();
    }

    /**
     * Determine if the object is locked or not.
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set whether the object is locked or not.
     *
     * @param boolean $locked The locked flag.
     * @return self
     */
    public function setLocked($locked)
    {
        $this->locked = !!$locked;

        return $this;
    }

    /**
     * Determine if the object can be viewed (on the front-end).
     *
     * @return boolean
     */
    public function isViewable()
    {
        return $this->id() && $this->isActive();
    }

    /**
     * Determine if the object can be deleted.
     *
     * @return boolean
     */
    public function isDeletable()
    {
        return $this->id() && !$this->isLocked();
    }

    /**
     * Determine if the object can be reset.
     *
     * @return boolean
     */
    public function isResettable()
    {
        return $this->id() && !$this->isLocked();
    }



    // Events
    // =========================================================================

    /**
     * Event called before _creating_ the object.
     *
     * @see    \Charcoal\Source\StorableTrait::preSave() For the "create" Event.
     * @return boolean
     */
    protected function preSave()
    {
        if (!$this->locked()) {
            $this->setSlug($this->generateSlug());
        }

        return parent::preSave();
    }

    /**
     * Event called after _creating_ the object.
     *
     * @see    \Charcoal\Source\StorableTrait::postSave() For the "create" Event.
     * @see    \Charcoal\Object\RoutableTrait::generateObjectRoute()
     * @return boolean
     */
    protected function postSave()
    {
        if (!$this->locked()) {
            $this->generateObjectRoute($this['slug']);
        }

        return parent::postSave();
    }

    /**
     * Event called before _updating_ the object.
     *
     * @see    \Charcoal\Source\StorableTrait::postUpdate() For the "update" Event.
     * @see    \Charcoal\Object\RoutableTrait::generateObjectRoute()
     * @param  array $properties Optional. The list of properties to update.
     * @return boolean
     */
    protected function preUpdate(array $properties = null)
    {
        if (!$this->locked()) {
            $this['slug'] = $this->generateSlug();
        }

        return parent::preUpdate($properties);
    }

    /**
     * Event called after _updating_ the object.
     *
     * @see    \Charcoal\Source\StorableTrait::postUpdate() For the "update" Event.
     * @see    \Charcoal\Object\RoutableTrait::generateObjectRoute()
     * @param  array $properties Optional. The list of properties to update.
     * @return boolean
     */
    protected function postUpdate(array $properties = null)
    {
        if (!$this->locked()) {
            $this->generateObjectRoute($this['slug']);
        }

        return parent::postUpdate($properties);
    }

    /**
     * Event called before _deleting_ the object.
     *
     * @see    \Charcoal\Model\AbstractModel::preDelete() For the "delete" Event.
     * @see    \Charcoal\Object\RoutableTrait::deleteObjectRoutes()
     * @return boolean
     */
    protected function preDelete()
    {
        if ($this->locked()) {
            return false;
        }

        $this->deleteObjectRoutes();

        return parent::preDelete();
    }
}
