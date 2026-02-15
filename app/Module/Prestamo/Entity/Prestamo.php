<?php

declare(strict_types=1);

namespace App\Module\Prestamo\Entity;

final readonly class Prestamo
{
    public function __construct(
        public string $usuarioId,
        public int $sindicatoId,
        public string $montoSolicitado,
        public string $tasaInteresAplicada,
        public ?int $id = null,
        public ?string $folio = null,
        public ?string $montoAprobado = null,
        public ?string $tasaMoratorioDiario = null,
        public ?string $totalAPagarEstimado = null,
        public ?string $saldoPendiente = null,
        public ?int $plazoMeses = null,
        public ?int $plazoQuincenas = null,
        public ?\DateTimeImmutable $fechaPrimerPago = null,
        public ?\DateTimeImmutable $fechaUltimoPagoProgramado = null,
        public ?\DateTimeImmutable $fechaSolicitud = null,
        public ?\DateTimeImmutable $fechaRevisionDocumental = null,
        public ?\DateTimeImmutable $fechaAprobacion = null,
        public ?\DateTimeImmutable $fechaGeneracionDocumentos = null,
        public ?\DateTimeImmutable $fechaValidacionFirmas = null,
        public ?\DateTimeImmutable $fechaDesembolso = null,
        public ?\DateTimeImmutable $fechaLiquidacionTotal = null,
        public string $estado = "borrador",
        public ?int $prestamoOrigenId = null,
        public ?string $motivoRechazo = null,
        public ?string $observacionesAdmin = null,
        public ?string $observacionesInternas = null,
        public ?string $firmanteFinanzas = null,
        public ?string $firmantePrestamista = null,
        public bool $requiereReestructuracion = false,
        public ?string $creadoPor = null,
        public ?\DateTimeImmutable $fechaEliminacion = null,
    ) {}
}
