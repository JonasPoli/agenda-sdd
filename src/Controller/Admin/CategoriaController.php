<?php

namespace App\Controller\Admin;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use App\Repository\CategoriaRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categorias', name: 'admin_categoria_')]
final class CategoriaController extends AbstractController
{
    private const MENSAGEM_POSSUI_EVENTOS = 'Não é possível excluir: a categoria possui eventos.';

    public function __construct(
        private readonly CategoriaRepository $categorias,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/categoria/index.html.twig', [
            'linhas' => $this->categorias->listarComTotalDeEventos(),
        ]);
    }

    #[Route('/nova', name: 'nova', methods: ['GET', 'POST'])]
    public function nova(Request $request): Response
    {
        return $this->salvar($request, new Categoria(), 'Nova categoria');
    }

    #[Route('/{id}/editar', name: 'editar', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editar(Request $request, Categoria $categoria): Response
    {
        return $this->salvar($request, $categoria, 'Editar categoria');
    }

    #[Route('/{id}/excluir', name: 'excluir', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function excluir(Request $request, Categoria $categoria): Response
    {
        if (!$this->isCsrfTokenValid('excluir_categoria_'.$categoria->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('erro', 'Token de segurança inválido. Recarregue a página e tente de novo.');

            return $this->redirectToRoute('admin_categoria_index');
        }

        if ($this->categorias->contarEventos($categoria) > 0) {
            $this->addFlash('erro', self::MENSAGEM_POSSUI_EVENTOS);

            return $this->redirectToRoute('admin_categoria_index');
        }

        try {
            $this->em->remove($categoria);
            $this->em->flush();
        } catch (ForeignKeyConstraintViolationException) {
            // Um evento foi vinculado entre a checagem e a exclusão: o banco recusa (ON DELETE RESTRICT)
            $this->addFlash('erro', self::MENSAGEM_POSSUI_EVENTOS);

            return $this->redirectToRoute('admin_categoria_index');
        }

        $this->addFlash('sucesso', 'Categoria excluída com sucesso.');

        return $this->redirectToRoute('admin_categoria_index');
    }

    private function salvar(Request $request, Categoria $categoria, string $titulo): Response
    {
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($categoria);
            $this->em->flush();
            $this->addFlash('sucesso', 'Categoria salva com sucesso.');

            return $this->redirectToRoute('admin_categoria_index');
        }

        return $this->render('admin/categoria/form.html.twig', [
            'form' => $form,
            'titulo' => $titulo,
        ]);
    }
}
