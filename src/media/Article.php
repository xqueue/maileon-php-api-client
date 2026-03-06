<?php

namespace de\xqueue\maileon\api\client\media;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

class Article extends AbstractJSONWrapper
{
    /** @var string|null */
    protected $id;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $path;

    /** @var string|null */
    protected $created;

    /** @var string[]|null */
    protected $tags = [];

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getTags()
    {
        return $this->tags ?: [];
    }
}
