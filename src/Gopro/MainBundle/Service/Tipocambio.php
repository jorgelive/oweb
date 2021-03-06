<?php

namespace Gopro\MainBundle\Service;

use \Symfony\Component\DependencyInjection\ContainerAwareInterface;
use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
use \Symfony\Component\DomCrawler\Crawler;


class Tipocambio implements ContainerAwareInterface{

    use ContainerAwareTrait;

    private $doctrine;

    function setDoctrine($doctrine){
        $this->doctrine = $doctrine;
        return $this;
    }

    function getDoctrine(){
        return $this->doctrine;
    }

    /**
     * @param mixed $mensaje
     * @return boolean
     */
    public function getTipodecambio(\DateTime $fecha)
    {

        $enDB = $this->getDoctrine()->getManager()->getRepository('GoproMaestroBundle:Tipocambio')
            ->findOneBy(['moneda' => 2, 'fecha' => $fecha]);

        if ($enDB){
            return $enDB;
        }

        $dia = str_pad($fecha->format('d'), 2, '0', STR_PAD_LEFT);
        $mes = str_pad($fecha->format('m'), 2, '0', STR_PAD_LEFT);;
        $ano = $fecha->format('Y');

        $tds = $this->crawl($fecha);

        if(empty($tds) || count($tds) < 13){
            error_log('La información es incompleta');
            return false;
        }elseif (count($tds) == 13){
            $fechaClone = clone $fecha;
            $fechaClone->sub(new \DateInterval('P1M'));
            $tds = $this->crawl($fechaClone);
            if(empty($tds) || count($tds) < 13){
                error_log('No existe informacion del mes anterior');
                return false;
            }
        }

        $tiposDelMes = $this->ordenarValores($tds, $mes, $ano);

        for ($i = (int)$dia; $i > 0; --$i) {
            if(isset($tiposDelMes[$ano . '-' . $mes .  '-' . str_pad($i, 2, '0', STR_PAD_LEFT)])) {
                return $this->insertTipo($tiposDelMes[$ano . '-' . $mes .  '-' . str_pad($i, 2, '0', STR_PAD_LEFT)], $fecha);
            }
        }

        return $this->insertTipo(end($tiposDelMes), $fecha);

    }

    private function insertTipo($tipo, $fecha){

        $em = $this->getDoctrine()->getManager();

        $moneda = $em->getReference('Gopro\MaestroBundle\Entity\Moneda', 2);

        $entity = new \Gopro\MaestroBundle\Entity\Tipocambio();
        $entity->setCompra($tipo['compra']);
        $entity->setVenta($tipo['venta']);
        $entity->setFecha($fecha);
        $entity->setMoneda($moneda);

        $em->persist($entity);
        $em->flush();

        return $entity;

    }


    private function crawl(\DateTime $fecha){
        $dom = new \DOMDocument;

        try{
            libxml_use_internal_errors(true);  //hides errors from invalid tags
            $dom->loadHTMLFile('http://www.sunat.gob.pe/cl-at-ittipcam/tcS01Alias?mes=' . $fecha->format('m') .'&anho=' .  $fecha->format('Y'));

        }
        catch(\Exception $e){
            error_log($e->getMessage());
            return false;
        }

        $crawler = new Crawler();
        $crawler->add($dom);

        if($crawler->filter('table')->count() < 2){
            error_log('Hay menos de dos tablas');
            return false;
        }
        $tables = $crawler->filter('table');

        $tds = array();
        foreach ($tables as $i => $table) {
            if($i == 1){ //el contenido es la segunda tabla

                $crawler = new Crawler($table); //la primera tabla es el contenido
                foreach ($crawler->filter('td') as $i => $node) {
                    $tds[] = trim($node->nodeValue);
                }
            }
        }

        return $tds;

    }


    private function ordenarValores($raw, $mes, $ano){
        $result = [];
        $itinerante = 0;
        $fecha = '';
        for ($i = 12; $i <= count($raw) -1 ; $i++) {
            $itinerante++;
            if($itinerante == 1){
                $fecha = $ano . '-' . $mes . '-' . str_pad($raw[$i], 2, '0', STR_PAD_LEFT);
                $result[$fecha]['date'] = new \DateTime($fecha);
            }elseif($itinerante == 2){
                $result[$fecha]['compra'] = $raw[$i];
            }elseif($itinerante == 3){
                $result[$fecha]['venta'] = $raw[$i];
                $itinerante = 0;
            }
        }
        return $result;
    }

}