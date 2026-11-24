<?php

namespace App\Controller\Admin;

use App\Repository\CategoriaRepository;
use App\Repository\EventoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PainelController extends AbstractController
{
    private const QUANTIDADE_PROXIMOS = 5;

    #[Route('/admin', name: 'admin_painel', methods: ['GET'])]
    public function index(CategoriaRepository $categorias, EventoRepository $eventos): Response
    {
        return $this->render('admin/painel/index.html.twig', [
            'total_categorias' => $categorias->count(),
            'total_eventos' => $eventos->count(),
            'proximos' => $eventos->proximos(self::QUANTIDADE_PROXIMOS),
        ]);
    }
}
