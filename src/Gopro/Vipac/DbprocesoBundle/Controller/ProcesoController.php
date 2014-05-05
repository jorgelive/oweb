<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Gopro\Vipac\DbprocesoBundle\Comun\Archivo as ArchivoOpe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class ProcesoController extends Controller
{


    public function cargaCp(){

    }

    /**
     * @Route("/proceso/cheque/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_cheque", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function chequeAction(Request $request,$archivoEjecutar)
    {
        $usuario=$this->get('security.context')->getToken()->getUser();

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'proceso_cheque'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('proceso_cheque');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_carga_generico'));
        }

        if($archivoEjecutar!==null){
            $archivoAlmacenado=$repositorio->find($archivoEjecutar);
        }
        if(isset($archivoAlmacenado)&&$archivoAlmacenado->getOperacion()=='proceso_cheque'){
            $tablaSpecs=array('schema'=>'RESERVAS',"nombre"=>'VVW_FILES_MERCADO');
            $columnaspecs[0]=array('nombre'=>'FECHA','llave'=>'no','tipo'=>'exceldate','proceso'=>'no');
            $columnaspecs[1]=null;
            $columnaspecs[2]=array('nombre'=>'ANO-NUM_FILE','llave'=>'si','tipo'=>'file');
            $columnaspecs[3]=null;
            $columnaspecs[4]=array('nombre'=>'MONTO','llave'=>'no','proceso'=>'no');
            $columnaspecs[5]=array('nombre'=>'CENTRO_COSTO','llave'=>'no');

            $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
            $procesoArchivo->setParametros($archivoAlmacenado->getAbsolutePath(),$tablaSpecs,$columnaspecs);
            $mensajes=$procesoArchivo->getMensajes();
            //print_r($procesoArchivo->getTablaSpecs());
            //print_r($procesoArchivo->getColumnaSpecs());
            if($procesoArchivo->parseExcel()!==false){
                $carga=$this->get('gopro_dbproceso_comun_cargador');
                $carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.default_connection'));
                $carga->cargaGenerica();
                $existente=$carga->getExistente();
                //print_r($existente);
                //print_r($procesoArchivo->getValoresIndizados());
                $fusion=array_replace_recursive($existente,$procesoArchivo->getValoresIndizados());
                $valido=true;
                foreach($procesoArchivo->getValoresIndizados() as $key=>$temp):
                    if (!array_key_exists($key, $existente)) {
                        $mensajes=array_merge($mensajes,array('El valor '.$key.' no se encuentra en la base de datos'));
                        $valido=false;
                    }
                endforeach;
                $resultado=array();
                if($valido===true){
                    foreach($fusion as $fusionPart):
                        if(!isset($resultados[$fusionPart['CENTRO_COSTO']])){
                            $resultados[$fusionPart['CENTRO_COSTO']]=0;
                        }
                        $resultados[$fusionPart['CENTRO_COSTO']]=$resultados[$fusionPart['CENTRO_COSTO']]+$fusionPart['MONTO'];
                    endforeach;

                }
                $mensajes=array_merge($mensajes,$carga->getMensajes());
            }else{
                $mensajes=array_merge($mensajes,array('El archivo no se puede procesar'));
            }
           //$mensajes = $this->get('gopro_dbproceso_comun_cargador')->ejecutar($tablaSpecs,$columnaSpecs,$valores);

        }else{
            $mensajes[]='El archivo no existe';
        }

        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'resultados'=>$resultados ,'mensajes' => $mensajes);
    }


}
