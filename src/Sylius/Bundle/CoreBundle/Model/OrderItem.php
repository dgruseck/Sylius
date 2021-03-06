<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Sylius\Bundle\CartBundle\Model\CartItem;
use Sylius\Bundle\CoreBundle\Model\InventoryUnit;
use Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\OrderBundle\Model\OrderItemInterface as BaseOrderItemInterface;

/**
 * Order item model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderItem extends CartItem implements OrderItemInterface
{
    /**
     * Product variant.
     *
     * @var VariantInterface
     */
    protected $variant;

    /**
     * Inventory units.
     *
     * @var Collection|InventoryUnitInterface[]
     */
    protected $inventoryUnits;

    public function __construct()
    {
        parent::__construct();

        $this->inventoryUnits = new ArrayCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->variant->getProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(VariantInterface $variant)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(BaseOrderItemInterface $item)
    {
        return parent::equals($item) || ($item instanceof self
            && $item->getVariant() === $this->variant
            && $item->getUnitPrice() === $this->getUnitPrice()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryUnits()
    {
        return $this->inventoryUnits;
    }

    /**
     * {@inheritdoc}
     */
    public function addInventoryUnit(InventoryUnitInterface $unit)
    {
        if (!$this->hasInventoryUnit($unit)) {
            $unit->setOrderItem($this);
            $this->inventoryUnits->add($unit);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeInventoryUnit(InventoryUnitInterface $unit)
    {
        $unit->setOrderItem(null);
        $this->inventoryUnits->removeElement($unit);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasInventoryUnit(InventoryUnitInterface $unit)
    {
        return $this->inventoryUnits->contains($unit);
    }
}
