<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FPDM;
use setasign\Fpdi\Fpdi;
use mikehaertl\pdftk\Pdf;

class HomeController extends AbstractController {

    public function index() {
        $number = random_int(0, 100);

        return $this->render('home/index.html.twig', [
                    'number' => $number,
        ]);
    }

    public function submit(Request $request) {

        $data = $request->request->all();
//        var_dump($data);
//        die();
        $root = $request->server->get('DOCUMENT_ROOT');
        
        $template = $root . '/attestation_de_deplacement_derogatoire.pdf';
        $tmp = $root . "/tmp";
        $fields = array(
            'name' => $data['name'],
            'naissance' => date('d-m-Y', strtotime($data['naissance'])),
            'adresse' => $data['adresse'],
            'choix' . $data['motif'] => "Oui",
            'ville' => $data['ville'],
            'jour' => date('d', strtotime($data['date'])),
            'mois' => date('m', strtotime($data['date'])),
        );


        $id = uniqid();
        $pdf_path = $tmp . "/" . $id . ".pdf";


        $pdf = new Pdf($template
                , ['command' => 'LD_PRELOAD='.$root.'/../pdftk/lib/libgcj.so.12 '.$root.'/../pdftk/bin/pdftk']
        );
        $pdf->fillForm($fields)
                //->needAppearances()
                ->flatten()
                ->execute();
        if($pdf->getError()){
            print_r($pdf->getError());die();
        }
        file_put_contents($pdf_path, $pdf->toString());

        $pdf = new Fpdi();
        $pdf->AddPage();
        $pdf->setSourceFile($pdf_path);
        $template = $pdf->importPage(1);
        $pdf->useTemplate($template);
        $img = $tmp . '/' . $id . '.png';
        $uri = substr($data['signature_val'], strpos($data['signature_val'], ",") + 1);

        file_put_contents($img, base64_decode($uri));

        $pdf->Image($img, 130, 250, 60, 60);

        unlink($pdf_path);
        unlink($img);

        return new Response($pdf->Output(), 200, array(
            "Content-Disposition" => "attachment; filename=Attestation_de_deplacement_derogatoire.pdf",
            "Content-Type" => "application/octet-stream",
        ));
    }

}
