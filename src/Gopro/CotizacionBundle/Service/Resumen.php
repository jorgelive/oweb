<?php

namespace Gopro\CotizacionBundle\Service;

use \Symfony\Component\Filesystem\Filesystem;
use \Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use \Symfony\Component\DependencyInjection\ContainerAwareInterface;
use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Resumen implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    private $doctrine;

    private $datosTabs;
    private $datosCotizacion;

    private $clasificacionTarifas = [];
    private $resumenClasificado = [];

    private $mensaje;

    function setDoctrine($doctrine){
        $this->doctrine = $doctrine;
    }

    function getDoctrine(){
        return $this->doctrine;
    }

    function procesar($id)
    {

        $cotizacion = $this->getDoctrine()
            ->getRepository('GoproCotizacionBundle:Cotizacion')
            ->find($id);

        if (!$cotizacion) {
            $this->mensaje = sprintf('No se puede encontrar el objeto con el identificador : %s', $id);
            return false;
        }

        $tipoCambio = $this->getDoctrine()
            ->getRepository('GoproMaestroBundle:Tipocambio')
            ->findOneBy(['moneda' => 2, 'fecha' => $cotizacion->getCreado()]);


        if(!$tipoCambio){
            $this->mensaje = sprintf('No se puede obtener la el tipo de cambio del dia %s.',  $cotizacion->getCreado()->format('Y-m-d') );
            return false;
        }

        $datosCotizacion = [];
        //para mostrar primero el itinerario
        $datosTabs['itinerario']['nombre'] = 'Itinerarios';
        $datosTabs['agenda']['nombre'] = 'Agenda';
        $datosTabs['incluye']['nombre'] = 'Detalle';
        $datosTabs['tarifas']['nombre'] = 'Tarifas';

        $datosCotizacion['file']['nombre'] = $cotizacion->getFile()->getNombre();
        $datosCotizacion['file']['pais'] = $cotizacion->getFile()->getPais()->getNombre();
        $datosCotizacion['file']['idioma'] = $cotizacion->getFile()->getIdioma()->getNombre();
        $datosCotizacion['cotizacion']['tipocambiocompra'] = $tipoCambio->getCompra();
        $datosCotizacion['cotizacion']['tipocambioventa'] = $tipoCambio->getVenta();
        $datosCotizacion['cotizacion']['comision'] = $cotizacion->getComision();
        $datosCotizacion['cotizacion']['nombre'] = $cotizacion->getNombre();
        $datosCotizacion['cotizacion']['numeropasajeros'] = $cotizacion->getNumeropasajeros();
        $datosCotizacion['cotizacion']['estadocotizacion'] = $cotizacion->getEstadocotizacion()->getNombre();

        if($cotizacion->getCotservicios()->count() > 0){
            foreach ($cotizacion->getCotservicios() as $servicio):

                if($servicio->getItinerario()->getItinerariodias()){
                    foreach ($servicio->getItinerario()->getItinerariodias() as $dias):
                        $fecha = clone($servicio->getFechahorainicio())->add(new \DateInterval('P' . ($dias->getDia() - 1) . 'D'));
                        if(!isset($datosTabs['itinerario']['itinerarios'][$fecha->format('ymd')]['descripcion'])){
                            $datosTabs['itinerario']['itinerarios'][$fecha->format('ymd')]['descripcion'] = '';
                        }
                        $datosTabs['itinerario']['itinerarios'][$fecha->format('ymd')]['fecha'] = strftime("%A, %d de %B de %Y", strtotime($fecha->format('Y-m-d')));
                        $datosTabs['itinerario']['itinerarios'][$fecha->format('ymd')]['descripcion'] .= '<h4>' . $dias->getTitulo() . '</h4>' . $dias->getContenido();
                    endforeach;
                }

                if($servicio->getCotcomponentes()->count() > 0){
                    foreach( $servicio->getCotcomponentes() as $componente):

                        $tempArraySchedule = [];
                        if(!empty($componente->getComponente()->getDuracion())){
                            $tempArraySchedule['fechahorainicio'] = $componente->getFechahorainicio();
                            $tempArraySchedule['fechahorafin'] = $componente->getFechahorafin();
                            $tempArraySchedule['titulo'] = $componente->getComponente()->getTitulo();
                            $datosCotizacionSchedule['schedule'][] = $tempArraySchedule;
                        }

                        if($componente->getCottarifas()->count() > 0){

                            $cantidadComponente = 0;

                            $tempArrayComponente = [];

                            foreach ($componente->getCottarifas() as $tarifa):

                                ////////Incluye//////

                                $tempArrayIncluye = [];

                                if(!empty($componente->getComponente()->getTitulo())){

                                    if(!empty($tarifa->getTarifa()->getTitulo())) {
                                        $tempArrayIncluye['titulo'] = $tarifa->getTarifa()->getTitulo();
                                        $tempArrayIncluye['cantidad'] = (int)($tarifa->getCantidad() * $componente->getCantidad());
                                        if(!empty($tarifa->getTarifa()->getValidezInicio())){
                                            $tempArrayIncluye['validezInicio'] = $tarifa->getTarifa()->getValidezInicio();
                                        }

                                        if(!empty($tarifa->getTarifa()->getValidezFin())){
                                            $tempArrayIncluye['validezFin'] = $tarifa->getTarifa()->getValidezFin();
                                        }

                                        if(!empty($tarifa->getTarifa()->getCapacidadmin())){
                                            $tempArrayIncluye['capacidadMin'] = $tarifa->getTarifa()->getCapacidadmin();
                                        }

                                        if(!empty($tarifa->getTarifa()->getCapacidadmax())){
                                            $tempArrayIncluye['capacidadMax'] = $tarifa->getTarifa()->getCapacidadmax();
                                        }

                                        if(!empty($tarifa->getTarifa()->getEdadmin())){
                                            $tempArrayIncluye['edadMin'] = $tarifa->getTarifa()->getEdadmin();
                                        }

                                        if(!empty($tarifa->getTarifa()->getEdadmax())){
                                            $tempArrayIncluye['edadMax'] = $tarifa->getTarifa()->getEdadmax();
                                        }

                                        if(!empty($tarifa->getTarifa()->getTipopax())){
                                            $tempArrayIncluye['tipoPaxId'] = $tarifa->getTarifa()->getTipopax()->getId();
                                            $tempArrayIncluye['tipoPaxNombre'] = $tarifa->getTarifa()->getTipopax()->getNombre();
                                        }

                                    }

                                    $datosTabs['incluye']['tipos'][$tarifa->getTipotarifa()->getId()]['titulo'] = $tarifa->getTipotarifa()->getTitulo();
                                    $datosTabs['incluye']['tipos'][$tarifa->getTipotarifa()->getId()]['componentes'][$componente->getComponente()->getId()]['titulo'] = $componente->getComponente()->getTitulo();
                                    if(!empty($tempArrayIncluye)){
                                        $datosTabs['incluye']['tipos'][$tarifa->getTipotarifa()->getId()]['componentes'][$componente->getComponente()->getId()]['tarifas'][] = $tempArrayIncluye;
                                    }

                                }

                                /////Tarifas////////

                                $tempArrayTarifa = [];
                                $tempArrayTarifa['id'] = $tarifa->getId();
                                $tempArrayTarifa['nombreServicio'] = $servicio->getServicio()->getNombre();
                                $tempArrayTarifa['nombreComponente'] = $componente->getComponente()->getNombre();
                                $tempArrayTarifa['tituloComponente'] = $componente->getComponente()->getTitulo();
                                $tempArrayTarifa['prorrateado'] = $tarifa->getTarifa()->getProrrateado();

                                if($tarifa->getTarifa()->getProrrateado() === true){
                                    $tempArrayTarifa['montounitario'] = number_format(
                                        (float)($tarifa->getMonto() * $tarifa->getCantidad() / $datosCotizacion['cotizacion']['numeropasajeros'] * $componente->getCantidad()
                                        ), 2, '.', '');
                                    $tempArrayTarifa['montototal'] = number_format(
                                        (float)($tarifa->getMonto() * $tarifa->getCantidad() * $componente->getCantidad()
                                        ),2, '.', '');
                                    $tempArrayTarifa['cantidad'] = (int)($datosCotizacion['cotizacion']['numeropasajeros']);

                                }else{
                                    $tempArrayTarifa['montounitario'] = number_format(
                                        (float)($tarifa->getMonto() * $componente->getCantidad()
                                        ),2, '.', '');
                                    $tempArrayTarifa['montototal'] = number_format(
                                        (float)($tarifa->getMonto() * $tarifa->getCantidad() * $componente->getCantidad()
                                        ), 2, '.', '');
                                    $tempArrayTarifa['cantidad'] = $tarifa->getCantidad();
                                    //solo sumo prorrateados
                                    $cantidadComponente += $tempArrayTarifa['cantidad'];
                                };

                                $tempArrayTarifa['nombre'] = $tarifa->getTarifa()->getNombre();
                                $tempArrayTarifa['titulo'] = $tarifa->getTarifa()->getTitulo();
                                $tempArrayTarifa['moneda'] = $tarifa->getMoneda()->getId();

                                //dolares = 2
                                if($tarifa->getMoneda()->getId() == 2){
                                    $tempArrayTarifa['montosoles'] = number_format((float)($tempArrayTarifa['montounitario'] * $tipoCambio->getCompra()), 2, '.', '');
                                    $tempArrayTarifa['montodolares'] = $tempArrayTarifa['montounitario'];
                                }elseif ($tarifa->getMoneda()->getId() == 1){
                                    $tempArrayTarifa['montosoles'] = $tempArrayTarifa['montounitario'];
                                    $tempArrayTarifa['montodolares'] = number_format((float)($tempArrayTarifa['montounitario'] / $tipoCambio->getVenta()), 2, '.', '');
                                }else{
                                    $this->addFlash('sonata_flash_error', 'La aplicación solo puede utilizar Soles y dólares en las tarifas.');
                                    return new RedirectResponse($this->admin->generateUrl('list'));
                                }

                                $factorComision = 1;
                                if($tarifa->getTipotarifa()->getComisionable() == true){
                                    $factorComision = 1 + ($cotizacion->getComision() / 100);
                                }

                                $tempArrayTarifa['ventasoles'] = number_format((float)($tempArrayTarifa['montosoles'] * $factorComision), 2, '.', '');
                                $tempArrayTarifa['ventadolares'] = number_format((float)($tempArrayTarifa['montodolares'] * $factorComision), 2, '.', '');


                                if(!empty($tarifa->getTarifa()->getValidezInicio())){
                                    $tempArrayTarifa['validezInicio'] = $tarifa->getTarifa()->getValidezInicio();
                                }

                                if(!empty($tarifa->getTarifa()->getValidezFin())){
                                    $tempArrayTarifa['validezFin'] = $tarifa->getTarifa()->getValidezFin();
                                }

                                if(!empty($tarifa->getTarifa()->getCapacidadmin())){
                                    $tempArrayTarifa['capacidadMin'] = $tarifa->getTarifa()->getCapacidadmin();
                                }

                                if(!empty($tarifa->getTarifa()->getCapacidadmax())){
                                    $tempArrayTarifa['capacidadMax'] = $tarifa->getTarifa()->getCapacidadmax();
                                }

                                if(!empty($tarifa->getTarifa()->getEdadmin())){
                                    $tempArrayTarifa['edadMin'] = $tarifa->getTarifa()->getEdadmin();
                                }

                                if(!empty($tarifa->getTarifa()->getEdadmax())){
                                    $tempArrayTarifa['edadMax'] = $tarifa->getTarifa()->getEdadmax();
                                }

                                if(!empty($tarifa->getTarifa()->getTipopax())){
                                    $tempArrayTarifa['tipoPaxId'] = $tarifa->getTarifa()->getTipopax()->getId();
                                    $tempArrayTarifa['tipoPaxNombre'] = $tarifa->getTarifa()->getTipopax()->getNombre();
                                }

                                $tempArrayTarifa['tipoTarId'] = $tarifa->getTipotarifa()->getId();
                                $tempArrayTarifa['tipoTarNombre'] = $tarifa->getTipotarifa()->getNombre();
                                $tempArrayTarifa['tipoTarTitulo'] = $tarifa->getTipotarifa()->getTitulo();

                                $this->completarTipoTarifa($tempArrayTarifa, $tarifa->getTarifa()->getProrrateado());

                                $tempArrayComponente['tarifas'][] = $tempArrayTarifa;

                            endforeach;

                            $tempArrayComponente['nombre'] = $componente->getComponente()->getNombre();
                            $tempArrayComponente['titulo'] = $componente->getComponente()->getTitulo();
                            $tempArrayComponente['fechahorainicio'] = $componente->getFechahorainicio();
                            $tempArrayComponente['fechahorafin'] = $componente->getFechahorafin();
                            $this->obtenerTarifasComponente($tempArrayComponente['tarifas']);

                            $datosTabs['agenda']['componentes'][] = $tempArrayComponente;

                            //no he sumado prorrateados puede ir en blanco para el caso de que solo exista prorrateado
                            if($cantidadComponente > 0 && $cantidadComponente != $cotizacion->getNumeropasajeros()){
                                $this->mensaje = sprintf('La cantidad de pasajeros por componente no coincide con la cantidad de pasajeros en %s %s %s.', $servicio->getFechahorainicio()->format('Y/m/d'), $servicio->getServicio()->getNombre(), $componente->getComponente()->getNombre());
                                return false;
                            }

                        }else{
                            $this->mensaje = sprintf('El componente no tiene tarifa en %s %s %s.', $servicio->getFechahorainicio()->format('Y/m/d'), $servicio->getServicio()->getNombre(), $componente->getComponente()->getNombre());
                            return false;
                        }

                    endforeach;

                }else{
                    $this->mensaje = sprintf('El servicio no tiene componente en %s %s.', $servicio->getFechahorainicio()->format('Y/m/d'), $servicio->getServicio()->getNombre());
                    return false;
                }

            endforeach;

        }else{
            $this->mensaje = 'El la cotización no tiene servicios.';
            return false;
        }

        if(!empty($this->clasificacionTarifas)){
            $this->resumenTarifas();
            $datosTabs['tarifas']['rangos'] = $this->clasificacionTarifas;
            $datosCotizacion['resumenClasificado'] = $this->resumenClasificado;
        }

        $this->datosTabs = $datosTabs;
        $this->datosCotizacion = $datosCotizacion;

        return true;
    }

    public function getMensaje(){
        return $this->mensaje;
    }

    public function getDatosTabs(){
        return $this->datosTabs;
    }

    public function getDatosCotizacion(){
        return $this->datosCotizacion;
    }

    public function resumenTarifas(){
        foreach ($this->clasificacionTarifas as &$clase):

            foreach ($clase['tarifa'] as $tarifa):
                $clase['resumen'][$tarifa['tipoTarId']]['tipoTarNombre'] = $tarifa['tipoTarNombre'];
                $clase['resumen'][$tarifa['tipoTarId']]['tipoTarTitulo'] = $tarifa['tipoTarTitulo'];

                $this->resumendeClasificado[$tarifa['tipoTarNombre']] = $tarifa['tipoTarNombre'];
                $this->resumendeClasificado[$tarifa['tipoTarTitulo']] = $tarifa['tipoTarTitulo'];

                if(!isset($this->resumendeClasificado[$tarifa['tipoTarId']]['montosoles'])){
                    $this->resumendeClasificado[$tarifa['tipoTarId']]['montosoles'] = 0;
                }
                $this->resumendeClasificado[$tarifa['tipoTarId']]['montosoles'] += $tarifa['montosoles'] * $clase['cantidad'];

                if(!isset($this->resumendeClasificado[$tarifa['tipoTarId']]['montodolares'])){
                    $this->resumendeClasificado[$tarifa['tipoTarId']]['montodolares'] = 0;
                }
                $this->resumendeClasificado[$tarifa['tipoTarId']]['montodolares'] += $tarifa['montodolares'] * $clase['cantidad'];

                if(!isset($this->resumendeClasificado[$tarifa['tipoTarId']]['ventasoles'])){
                    $this->resumendeClasificado[$tarifa['tipoTarId']]['ventasoles'] = 0;
                }
                $this->resumendeClasificado[$tarifa['tipoTarId']]['ventasoles'] += $tarifa['ventasoles'] * $clase['cantidad'];

                if(!isset($this->resumendeClasificado[$tarifa['tipoTarId']]['ventadolares'])){
                    $this->resumendeClasificado[$tarifa['tipoTarId']]['ventadolares'] = 0;
                }
                $this->resumendeClasificado[$tarifa['tipoTarId']]['ventadolares'] += $tarifa['ventadolares'] * $clase['cantidad'];

                if(!isset($clase['resumen'][$tarifa['tipoTarId']]['montosoles'])){
                    $clase['resumen'][$tarifa['tipoTarId']]['montosoles'] = 0;
                }
                $clase['resumen'][$tarifa['tipoTarId']]['montosoles'] += $tarifa['montosoles'];

                if(!isset($clase['resumen'][$tarifa['tipoTarId']]['montodolares'])){
                    $clase['resumen'][$tarifa['tipoTarId']]['montodolares'] = 0;
                }
                $clase['resumen'][$tarifa['tipoTarId']]['montodolares'] += $tarifa['montodolares'];

                if(!isset($clase['resumen'][$tarifa['tipoTarId']]['ventasoles'])){
                    $clase['resumen'][$tarifa['tipoTarId']]['ventasoles'] = 0;
                }
                $clase['resumen'][$tarifa['tipoTarId']]['ventasoles'] += $tarifa['ventasoles'];

                if(!isset($clase['resumen'][$tarifa['tipoTarId']]['ventadolares'])){
                    $clase['resumen'][$tarifa['tipoTarId']]['ventadolares'] = 0;
                }
                $clase['resumen'][$tarifa['tipoTarId']]['ventadolares'] += $tarifa['ventadolares'];
            endforeach;

        endforeach;
    }


    private function obtenerTarifasComponente($componente){

        $claseTarifas = [];

        $tiposAux=[];

        //se ejecuta bucle para detectar tipo duplicado
        foreach ($componente as $id => $tarifa):
            $titulo = [];
            $nombre = [];

            $temp = [];
            if(!empty($tarifa['titulo'])){
                $titulo[] = $tarifa['titulo'];
            }
            if(!empty($tarifa['tituloComponente'])){
                $titulo[] = $tarifa['tituloComponente'];
            }

            if(!empty($tarifa['nombre'])){
                $nombre[] = $tarifa['nombre'];
            }
            if(!empty($tarifa['nombreComponente'])){
                $nombre[] = $tarifa['nombreComponente'];
            }

            if(empty($tarifa['rangoEdad']) && empty($tarifa['tipoPaxId'])){
                $tipo = 'r0t0';
            }elseif(empty($tarifa['rangoEdad'])){
                $tipo = 't' . $tarifa['tipoPaxId'];
            }elseif(empty($tarifa['tipoPaxId'])){
                $tipo = 'r' . $tarifa['rangoEdad'];
            }else{
                $tipo = 'r' . $tarifa['rangoEdad']. 't' . $tarifa['tipoPaxId'];
            }

            $temp['tipo'] = $tipo;
            $temp['generarNuevo'] = false;
            if(array_search($temp['tipo'], $tiposAux, true) != false){
                $temp['generarNuevo'] = true;
            }

            $temp['titulo'] = implode(' - ', $titulo);
            $temp['nombre'] = implode(' - ', $nombre);
            $temp['cantidad'] = $tarifa['cantidad'];
            $temp['tipoPaxId'] = $tarifa['tipoPaxId'];
            $temp['tipoPaxNombre'] = $tarifa['tipoPaxNombre'];
            $temp['rangoEdad'] = $tarifa['rangoEdad'];
            $temp['rangoEdadNombre'] = $tarifa['rangoEdadNombre'];

            $temp['tarifa'] = $tarifa;

            $claseTarifas[] = $temp;

            if($tarifa['prorrateado'] !== true){
                $tiposAux[] = $tipo;
            }
        endforeach;

        if(count($claseTarifas) > 0){
            $this->procesarTarifa($claseTarifas, 0);
            $this->resetClasificacionTarifas();

        }
    }

    private function resetClasificacionTarifas(){

        foreach ($this->clasificacionTarifas as &$clase):
            $clase['cantidadRestante'] = $clase['cantidad'];

        endforeach;
    }

    private function procesarTarifa($claseTarifas, $ejecucion){

        $ejecucion++;

        if(empty($this->clasificacionTarifas)){
            foreach ($claseTarifas as $keyClase => &$clase):

                $auxClase = [];
                $auxClase['tipo'] = $clase['tipo'];
                $auxClase['nombre'] = $clase['nombre'];
                $auxClase['titulo'] = $clase['titulo'];
                $auxClase['cantidad'] = $clase['cantidad'];
                $auxClase['cantidadRestante'] = $clase['cantidad'];
                $auxClase['tipoPaxId'] = $clase['tipoPaxId'];
                $auxClase['tipoPaxNombre'] = $clase['tipoPaxNombre'];
                $auxClase['rangoEdad'] = $clase['rangoEdad'];
                $auxClase['rangoEdadNombre'] = $clase['rangoEdadNombre'];
                unset($clase['tarifa']['cantidad']);
                unset($clase['tarifa']['montototal']);
                $auxClase['tarifa'][] = $clase['tarifa'];
                $this->clasificacionTarifas[] = $auxClase;

            endforeach;

            return;
        }


        foreach ($claseTarifas as $keyClase => &$clase):

            $voterIndex = $this->voter($clase);

            if($voterIndex !== false){

                //paso elarray principal para adicionar elemento como esta por referencia
                $this->modificarClasificacion($clase, $voterIndex, $clase['generarNuevo']);

                if($clase['cantidad'] < 1){
                    unset($claseTarifas[$keyClase]);
                }
            }

        endforeach;

        foreach ($claseTarifas as $keyClase => &$clase):

            $voterIndex = $this->voter($clase);

            if($voterIndex !== false){
                $this->match($clase, $voterIndex);

                if($clase['cantidad'] < 1){
                    unset($claseTarifas[$keyClase]);
                }
            }

        endforeach;

        if($ejecucion < 10 && count($claseTarifas) > 0){
            $this->procesarTarifa($claseTarifas, $ejecucion);
        }

        //si despues del proceso hay tarifas muestro error
        if(count($claseTarifas) > 0){
            // todo revisar esto
            //var_dump(sprintf('Hay tarifas que no pudieron ser clasificadas despues de %d ejecuciones, revise: %s.', $ejecucion, reset($claseTarifas)['nombre']));
            //$this->addFlash('sonata_flash_error', sprintf('Hay tarifas que no pudieron ser clasificadas despues de %d ejecuciones, revise: %s.', $ejecucion, reset($claseTarifas)['nombre']));
            //return new RedirectResponse($this->admin->generateUrl('list'));
        }
    }

    private function modificarClasificacion(&$clase, $voterIndex, $forzarNuevo = false){

        if($forzarNuevo === true
            || ($clase['tipoPaxId'] != $this->clasificacionTarifas[$voterIndex]['tipoPaxId'] && $clase['tipoPaxId'] != 0)
            || ($clase['rangoEdad'] != $this->clasificacionTarifas[$voterIndex]['rangoEdad'] && $clase['rangoEdad'] != 0)){
            if($clase['cantidad'] == $this->clasificacionTarifas[$voterIndex]['cantidad']){
                $this->clasificacionTarifas[$voterIndex]['rangoEdad'] = $clase['rangoEdad'];
                $this->clasificacionTarifas[$voterIndex]['rangoEdad'] = $clase['rangoEdadNombre'];
                $this->clasificacionTarifas[$voterIndex]['tipoPaxId'] = $clase['tipoPaxId'];
                $this->clasificacionTarifas[$voterIndex]['tipoPaxId'] = $clase['tipoPaxNombre'];
                $this->clasificacionTarifas[$voterIndex]['tipo'] = $clase['tipo'];
                $this->clasificacionTarifas[$voterIndex]['nombre'] = $clase['nombre'];
                $this->clasificacionTarifas[$voterIndex]['titulo'] = $clase['titulo'];

            }elseif($clase['cantidad'] < $this->clasificacionTarifas[$voterIndex]['cantidad']){

                $temp = $this->clasificacionTarifas[$voterIndex];
                $temp['rangoEdad'] = $clase['rangoEdad'];
                $temp['rangoEdad'] = $clase['rangoEdadNombre'];
                $temp['tipoPaxId'] = $clase['tipoPaxId'];
                $temp['tipoPaxId'] = $clase['tipoPaxNombre'];
                $temp['tipo'] = $clase['tipo'];
                $temp['nombre'] = $clase['nombre'];
                $temp['titulo'] = $clase['titulo'];
                $temp['cantidad'] = $clase['cantidad'];
                $temp['cantidadRestante'] = $clase['cantidad'];

                if($forzarNuevo === true){
                    $temp['nombrePersistente'] = $clase['nombre'];
                    $temp['tituloPeristente'] = $clase['titulo'];
                }

                $this->clasificacionTarifas[] = $temp;

                $this->clasificacionTarifas[$voterIndex]['cantidad'] = $this->clasificacionTarifas[$voterIndex]['cantidad'] - $clase['cantidad'];
                $this->clasificacionTarifas[$voterIndex]['cantidadRestante'] = $this->clasificacionTarifas[$voterIndex]['cantidadRestante'] - $clase['cantidad'];
                if($forzarNuevo === true){
                    $this->clasificacionTarifas[$voterIndex]['nombrePersistente'] = $this->clasificacionTarifas[$voterIndex]['nombre'];
                    $this->clasificacionTarifas[$voterIndex]['tituloPeristente'] = $this->clasificacionTarifas[$voterIndex]['titulo'];
                }
            }else{

                //todo: sera necesario??
            }

        }else{
            //actualizamos nombres
            $this->clasificacionTarifas[$voterIndex]['nombre'] = $clase['nombre'];
            $this->clasificacionTarifas[$voterIndex]['titulo'] = $clase['titulo'];
        }

    }

    private function match(&$clase, $voterIndex){

        if($clase['cantidad'] == $this->clasificacionTarifas[$voterIndex]['cantidadRestante']){
            $clase['cantidad'] = 0;
            $this->clasificacionTarifas[$voterIndex]['cantidadRestante'] = 0;
            unset($clase['tarifa']['cantidad']);
            unset($clase['tarifa']['montototal']);
            $this->clasificacionTarifas[$voterIndex]['tarifa'][] = $clase['tarifa'];
        }elseif($clase['cantidad'] < $this->clasificacionTarifas[$voterIndex]['cantidadRestante']){

            $this->clasificacionTarifas[$voterIndex]['cantidadRestante'] = $this->clasificacionTarifas[$voterIndex]['cantidadRestante'] - $clase['cantidad'];
            $clase['cantidad'] = 0;
            unset($clase['tarifa']['cantidad']);
            unset($clase['tarifa']['montototal']);
            $this->clasificacionTarifas[$voterIndex]['tarifa'][] = $clase['tarifa'];
        }else{
            $clase['cantidad'] = $clase['cantidad'] - $this->clasificacionTarifas[$voterIndex]['cantidadRestante'];
            $this->clasificacionTarifas[$voterIndex]['cantidadRestante'] = 0;
            unset($clase['tarifa']['cantidad']);
            unset($clase['tarifa']['montototal']);
            $this->clasificacionTarifas[$voterIndex]['tarifa'][] = $clase['tarifa'];
        }
    }

    private function voter($clase){

        $clasificacion = $this->clasificacionTarifas;

        $voter = [];

        foreach ($clasificacion as $keyTarifa => $tarifaClasificada):

            $voter[$keyTarifa] = 0;

            if($tarifaClasificada['cantidadRestante'] > 0 &&
                ($clase['tipoPaxId'] == $tarifaClasificada['tipoPaxId'] ||
                    $clase['tipoPaxId'] == 0 ||
                    $tarifaClasificada['tipoPaxId'] == 0)
            ){
                if($tarifaClasificada['rangoEdad'] == $clase['rangoEdad']){
                    $voter[$keyTarifa] += 3;
                    if($tarifaClasificada['cantidad'] == $clase['cantidad']){
                        $voter[$keyTarifa] += 1;
                    }

                }elseif(
                    substr($tarifaClasificada['rangoEdad'], 0, 1) == substr($clase['rangoEdad'], 0, 1) ||
                    $clase['rangoEdad'] == 0 ||
                    $tarifaClasificada['rangoEdad'] == 0
                ){
                    $voter[$keyTarifa] += 1;
                    if($tarifaClasificada['cantidad'] == $clase['cantidad']){
                        $voter[$keyTarifa] += 1;
                    }
                }
            }

        endforeach;

        if(max($voter) > 0) {

            return array_search(max($voter), $voter);
        }else{

            return false;
        }

    }

    private function completarTipoTarifa(&$tarifa, $prorrateado){

        if($prorrateado === true){
            $tarifa['rangoEdad'] = 0;
            $tarifa['rangoEdadNombre'] = 'Cualquier Edad';

            $tarifa['tipoPaxId'] = 0;
            $tarifa['tipoPaxNombre'] = 'Todos';

            return;
        }

        if(!isset($tarifa['edadMin']) && !isset($tarifa['edadMax'])){
            $tarifa['rangoEdad'] = 0;
            $tarifa['rangoEdadNombre'] = 'Cualquier Edad';

            $tarifa['tipoPaxId'] = 0;
            $tarifa['tipoPaxNombre'] = 'Todos';

            return;
        }

        $min = 0;
        $max = 90;

        if(isset($tarifa['edadMin'])){
            $min = $tarifa['edadMin'];
        }

        if(isset($tarifa['edadMax'])){
            $max = $tarifa['edadMax'];
        }
        $promedio = ($min + $max) / 2;

        if($promedio < 6){
            $tarifa['rangoEdad'] = 'n1';
            $tarifa['rangoEdadNombre'] = 'Niño';
        }elseif($promedio >= 6 && $promedio < 20 ){
            $tarifa['rangoEdad'] = 'n2';
            $tarifa['rangoEdadNombre'] = 'Estudiante';
        }elseif($promedio >= 21 && $promedio < 59 ){
            $tarifa['rangoEdad'] = 'a1';
            $tarifa['rangoEdadNombre'] = 'Adulto';
        }else{
            $tarifa['rangoEdad'] = 'a2';
            $tarifa['rangoEdadNombre'] = 'Adulto Mayor';
        }

        return;
    }



}