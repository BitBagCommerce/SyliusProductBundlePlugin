<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Controller;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Sylius\Bundle\OrderBundle\Controller\OrderItemController as BaseOrderItemController;
use Sylius\Bundle\ResourceBundle\Controller;
use Sylius\Component\Order\CartActions;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderItemController extends BaseOrderItemController
{
    /** @var MessageBusInterface */
    protected $messageBus;

    public function __construct(
        MetadataInterface $metadata,
        Controller\RequestConfigurationFactoryInterface $requestConfigurationFactory,
        Controller\ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        FactoryInterface $factory,
        Controller\NewResourceFactoryInterface $newResourceFactory,
        EntityManagerInterface $manager,
        Controller\SingleResourceProviderInterface $singleResourceProvider,
        Controller\ResourcesCollectionProviderInterface $resourcesFinder,
        Controller\ResourceFormFactoryInterface $resourceFormFactory,
        Controller\RedirectHandlerInterface $redirectHandler,
        Controller\FlashHelperInterface $flashHelper,
        Controller\AuthorizationCheckerInterface $authorizationChecker,
        Controller\EventDispatcherInterface $eventDispatcher,
        Controller\StateMachineInterface $stateMachine,
        Controller\ResourceUpdateHandlerInterface $resourceUpdateHandler,
        Controller\ResourceDeleteHandlerInterface $resourceDeleteHandler,
        MessageBusInterface $messageBus
    ) {
        parent::__construct(
            $metadata,
            $requestConfigurationFactory,
            $viewHandler,
            $repository,
            $factory,
            $newResourceFactory,
            $manager,
            $singleResourceProvider,
            $resourcesFinder,
            $resourceFormFactory,
            $redirectHandler,
            $flashHelper,
            $authorizationChecker,
            $eventDispatcher,
            $stateMachine,
            $resourceUpdateHandler,
            $resourceDeleteHandler
        );

        $this->messageBus = $messageBus;
    }

    public function addProductBundleAction(Request $request): ?Response
    {
        $cart = $this->getCurrentCart();
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, CartActions::ADD);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->newResourceFactory->create($configuration, $this->factory);

        $this->getQuantityModifier()->modify($orderItem, 1);

        /** @var ProductInterface $product */
        $product = $orderItem->getProduct();
        assert(null !== $configuration->getFormType());
        $form = $this->getFormFactory()->create(
            $configuration->getFormType(),
            new AddProductBundleToCartCommand($cart, $orderItem, $product),
            $configuration->getFormOptions()
        );

        if ($request->isMethod(Request::METHOD_POST) && $form->handleRequest($request)->isValid()) {
            return $this->handleForm($form, $configuration, $orderItem, $request);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->handleBadAjaxRequestView($configuration, $form);
        }

        return $this->render(
            $configuration->getTemplate(CartActions::ADD . '.html'),
            [
                'configuration' => $configuration,
                $this->metadata->getName() => $orderItem,
                'form' => $form->createView(),
            ]
        );
    }

    private function handleForm(
        FormInterface $form,
        Controller\RequestConfiguration $configuration,
        OrderItemInterface $orderItem,
        Request $request
    ): ?Response {
        /** @var AddProductBundleToCartCommand $addProductBundleToCartCommand */
        $addProductBundleToCartCommand = $form->getData();
        $errors = $this->getCartItemErrors($addProductBundleToCartCommand->getCartItem());
        if (0 < count($errors)) {
            $form = $this->getAddToCartFormWithErrors($errors, $form);

            return $this->handleBadAjaxRequestView($configuration, $form);
        }
        $event = $this->eventDispatcher->dispatchPreEvent(CartActions::ADD, $configuration, $orderItem);
        if ($event->isStopped() && !$configuration->isHtmlRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }
        if ($event->isStopped()) {
            $this->flashHelper->addFlashFromEvent($configuration, $event);

            return $this->redirectHandler->redirectToIndex($configuration, $orderItem);
        }
        $this->messageBus->dispatch($addProductBundleToCartCommand);
        $resourceControllerEvent = $this->eventDispatcher->dispatchPostEvent(CartActions::ADD, $configuration, $orderItem);
        if ($resourceControllerEvent->hasResponse()) {
            return $resourceControllerEvent->getResponse();
        }
        $this->flashHelper->addSuccessFlash($configuration, CartActions::ADD, $orderItem);

        if ($request->isXmlHttpRequest()) {
            assert(null !== $this->viewHandler);
            $response = $this->viewHandler->handle($configuration, View::create([], Response::HTTP_CREATED));
        } else {
            $response = $this->redirectHandler->redirectToResource($configuration, $orderItem);
        }

        return $response;
    }
}
