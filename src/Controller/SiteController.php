<?php

namespace App\Controller;

use App\Repository\EventoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SiteController extends AbstractController
{
    public function __construct(private readonly EventoRepository $eventos)
    {
    }

    #[Route('/', name: 'site_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('site/home.html.twig', [
            'eventos' => $this->eventos->publicadosAPartirDeHoje(),
        ]);
    }

    /**
     * Qualquer evento publicado abre, inclusive os que já aconteceram. Rascunho ou slug inexistente recebem
     * a mesma página "Evento não encontrado" com status 404, para não revelar que um rascunho existe.
     */
    #[Route('/evento/{slug}', name: 'site_evento', methods: ['GET'])]
    public function evento(string $slug): Response
    {
        $evento = $this->eventos->buscarPublicadoPorSlug($slug);

        if (null === $evento) {
            return $this->render('site/evento_nao_encontrado.html.twig', [], new Response(status: Response::HTTP_NOT_FOUND));
        }

        return $this->render('site/evento.html.twig', [
            'evento' => $evento,
        ]);
    }
}
