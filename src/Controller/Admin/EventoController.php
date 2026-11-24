<?php

namespace App\Controller\Admin;

use App\Entity\Evento;
use App\Form\EventoType;
use App\Repository\EventoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/eventos', name: 'admin_evento_')]
final class EventoController extends AbstractController
{
    public function __construct(
        private readonly EventoRepository $eventos,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/evento/index.html.twig', [
            'eventos' => $this->eventos->listarParaAdmin(),
        ]);
    }

    #[Route('/novo', name: 'novo', methods: ['GET', 'POST'])]
    public function novo(Request $request): Response
    {
        return $this->salvar($request, new Evento(), 'Novo evento');
    }

    #[Route('/{id}/editar', name: 'editar', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editar(Request $request, Evento $evento): Response
    {
        return $this->salvar($request, $evento, 'Editar evento');
    }

    #[Route('/{id}/excluir', name: 'excluir', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function excluir(Request $request, Evento $evento): Response
    {
        if (!$this->isCsrfTokenValid('excluir_evento_'.$evento->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('erro', 'Token de segurança inválido. Recarregue a página e tente de novo.');

            return $this->redirectToRoute('admin_evento_index');
        }

        $this->em->remove($evento);
        $this->em->flush();
        $this->addFlash('sucesso', 'Evento excluído com sucesso.');

        return $this->redirectToRoute('admin_evento_index');
    }

    private function salvar(Request $request, Evento $evento, string $titulo): Response
    {
        $form = $this->createForm(EventoType::class, $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($evento);
            $this->em->flush();
            $this->addFlash('sucesso', 'Evento salvo com sucesso.');

            return $this->redirectToRoute('admin_evento_index');
        }

        return $this->render('admin/evento/form.html.twig', [
            'form' => $form,
            'titulo' => $titulo,
            'evento' => $evento,
        ]);
    }
}
