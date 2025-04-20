<?php

namespace Dashify\DashifyBundle\Controller;

use Dashify\DashifyBundle\Attribute\AsDashifyResource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ResourceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, string $resource): Response
    {
        $attribute = $this->getResourceAttribute($resource);
        if (!$attribute) {
            throw $this->createNotFoundException('Resource not found');
        }

        $repository = $this->entityManager->getRepository($resource);
        $items = $repository->findAll();

        return $this->render('@Dashify/resource/index.html.twig', [
            'items' => $items,
            'resource' => $resource,
            'attribute' => $attribute,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, string $resource): Response
    {
        $attribute = $this->getResourceAttribute($resource);
        if (!$attribute) {
            throw $this->createNotFoundException('Resource not found');
        }

        $entity = new $resource();
        $form = $this->createForm($resource . 'Type', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->addFlash('success', 'Resource created successfully');
            return $this->redirectToRoute('dashify_' . strtolower($this->getResourceName($resource)) . '_index');
        }

        return $this->render('@Dashify/resource/create.html.twig', [
            'form' => $form->createView(),
            'resource' => $resource,
            'attribute' => $attribute,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, string $resource, int $id): Response
    {
        $attribute = $this->getResourceAttribute($resource);
        if (!$attribute) {
            throw $this->createNotFoundException('Resource not found');
        }

        $entity = $this->entityManager->getRepository($resource)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Entity not found');
        }

        $form = $this->createForm($resource . 'Type', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Resource updated successfully');
            return $this->redirectToRoute('dashify_' . strtolower($this->getResourceName($resource)) . '_index');
        }

        return $this->render('@Dashify/resource/edit.html.twig', [
            'form' => $form->createView(),
            'resource' => $resource,
            'attribute' => $attribute,
            'entity' => $entity,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, string $resource, int $id): Response
    {
        $entity = $this->entityManager->getRepository($resource)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Entity not found');
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->addFlash('success', 'Resource deleted successfully');
        return $this->redirectToRoute('dashify_' . strtolower($this->getResourceName($resource)) . '_index');
    }

    private function getResourceAttribute(string $class): ?AsDashifyResource
    {
        $reflection = new \ReflectionClass($class);
        $attributes = $reflection->getAttributes(AsDashifyResource::class);
        
        if (empty($attributes)) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    private function getResourceName(string $class): string
    {
        $parts = explode('\\', $class);
        return end($parts);
    }
} 