<?php

namespace Charcoal\Support\Cms\Object;

// From 'charcoal-base'
use Charcoal\Object\Content;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Cms\Metatag\HasMetatagTrait;
use Charcoal\Support\Cms\Metatag\HasOpenGraphTrait;
use Charcoal\Support\Cms\Metatag\HasTwitterCardTrait;
use Charcoal\Support\Cms\Object\WebContentInterface;
use Charcoal\Support\Property\ParsableValueTrait;

/**
 * Hypertext Content Model
 */
abstract class AbstractWebContent extends Content implements
    WebContentInterface
{
    use HasMetatagTrait;
    use HasOpenGraphTrait;
    use HasTwitterCardTrait;
    use ParsableValueTrait;

    /**
     * Objects are not locked by default.
     *
     * @var boolean
     */
    private $locked = false;

    /**
     * Determine if the object is locked or not.
     *
     * @return boolean
     */
    public function locked()
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
     * Determine if the object can be deleted.
     *
     * @return boolean
     */
    public function isDeletable()
    {
        return !!$this->id() && !$this->locked();
    }



    // Events
    // =========================================================================

    /**
     * Event called before _creating_ the object.
     *
     * @see    \Charcoal\Source\StorableTrait::preSave() For the "create" Event.
     * @return boolean
     */
    public function preSave()
    {
        $this->setSlug($this->generateSlug());

        return parent::preSave();
    }

    /**
     * Event called before _updating_ the object.
     *
     * @see    \Charcoal\Source\StorableTrait::postUpdate() For the "update" Event.
     * @see    \Charcoal\Object\RoutableTrait::generateObjectRoute()
     * @param  array $properties Optional. The list of properties to update.
     * @return boolean
     */
    public function preUpdate(array $properties = null)
    {
        $this->setSlug($this->generateSlug());

        return parent::preUpdate($properties);
    }

    /**
     * Event called before _deleting_ the object.
     *
     * @see    \Charcoal\Model\AbstractModel::preDelete() For the "delete" Event.
     * @see    \Charcoal\Attachment\Traits\AttachmentAwareTrait::removeJoins
     * @return boolean
     */
    public function preDelete()
    {
        if ($this->locked()) {
            return false;
        }

        return parent::preDelete();
    }
}
