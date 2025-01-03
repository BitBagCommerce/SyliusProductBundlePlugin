<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Component\Product;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Factory\AddProductBundleToCartDtoFactory;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactory;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\AddToCartFormComponent as BaseAddToCartFormComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductLivePropTrait;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductVariantLivePropTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class AddToCartFormComponent extends BaseAddToCartFormComponent
{
    //TODO there should be decoration instead of extension ? To be tested
    use ComponentToolsTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ProductLivePropTrait;
    use ProductVariantLivePropTrait;
    use TemplatePropTrait;

    /**
     * @param CartItemFactoryInterface<OrderItem> $cartItemFactory
     * @param class-string $formClass
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     * @param ProductVariantRepositoryInterface<ProductVariantInterface> $productVariantRepository
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly ObjectManager $manager,
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CartContextInterface $cartContext,
        private readonly AddToCartCommandFactory $addToCartCommandFactory,
        private readonly CartItemFactoryInterface $cartItemFactory,
        private readonly string $formClass,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        private readonly AddProductBundleToCartDtoFactory $addProductBundleToCartDtoFactory,

    ) {
        $this->initializeProduct($productRepository);
        $this->initializeProductVariant($productVariantRepository);

        parent::__construct($this->formFactory,
            $this->manager,
            $this->router,
            $this->requestStack,
            $this->eventDispatcher,
            $this->cartContext,
            $this->addToCartCommandFactory,
            $this->cartItemFactory,
            $this->formClass,
            $this->productRepository,
            $this->productVariantRepository,
        );
    }

    #[LiveAction]
    public function addToCart(): RedirectResponse
    {
        $this->submitForm();
        $addToCartCommand = $this->getForm()->getData();

        $this->eventDispatcher->dispatch(new GenericEvent($addToCartCommand), SyliusCartEvents::CART_ITEM_ADD);
        $this->manager->persist($addToCartCommand->getCart());
        $this->manager->flush();

        FlashBagProvider
            ::getFlashBag($this->requestStack)
            ->add('success', 'sylius.cart.add_item');

        return new RedirectResponse($this->router->generate(
            $this->routeName,
            $this->routeParameters,
        ));
    }

    protected function instantiateForm(): FormInterface
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->cartItemFactory->createForProduct($this->product);
        /** @var ProductInterface $orderProduct */
        $orderProduct = $orderItem->getProduct();

        $addToCartCommand = $this->addProductBundleToCartDtoFactory->createNew(
            $this->cartContext->getCart(),
            $orderItem,
            $orderProduct,
        );

        return $this->formFactory->create($this->formClass, $addToCartCommand, ['product' => $this->product]);
    }
}
