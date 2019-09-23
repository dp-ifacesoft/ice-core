<?php

namespace Ifacesoft\Ice\Core\V2\Infrastructure;

use Exception;
use Ifacesoft\Ice\Core\V2\Application\Service;
use Ifacesoft\Ice\Core\V2\Domain\Dto;

abstract class Repository extends Service
{
    /**
     * @param $id
     * @return Dto
     * @throws Exception
     */
    final public function get($id)
    {
        if (!$id) {
            throw new \RuntimeException('Dto id is empty in ' . get_class($this));
        }

        return $this->getParam($id);
    }

    /**
     * @param Dto[] $entities
     *
     * @return Repository
     *
     * @throws Exception
     */
    final public function add(array $entities)
    {
        $this->getParam()->set(
            $entities,
            [
                'callbacks' => static function ($alias, $entity) {
                    /** @var Dto $entity */
                    return [$entity->getId(), $entity];
                }
            ]
        );

        return $this;
    }

    /**
     * @param array $entities
     *
     * @return Repository
     *
     * @throws Exception
     */
    final public function remove(array $entities)
    {
        $this->getParam()->delete($entities);

        return $this;
    }
}