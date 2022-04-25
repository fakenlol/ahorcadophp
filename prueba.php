<?php

interface Jugador {
    public function sugerirLetra(string $tablero):string;
}

interface Tablero {
    public function elegirNuevaPalabra();
    public function mostrarTablero():string;
    public function palabraAdivinada():bool;
    public function intentarLetra(string $letra):bool;
}

class juego {
    public function jugar(Tablero $tablero, Jugador $jugador, int $intentos){

        echo $tablero->mostrarTablero() . "\n\n";

        do{
            $partida = $tablero->mostrarTablero();
            $letra = $jugador->sugerirLetra($partida);
            $tablero->intentarLetra($letra);
            if($tablero->palabraAdivinada() == TRUE){
                return "El jugador gano, la palabra secreta era: " . $tablero->palabraSecretaPublica . "\n";
            }
            $intentos--;
        }while($intentos != -1);

        return "El jugador perdio, la palabra secreta era: " . $tablero->palabraSecretaPublica  . "\n";
    }

    public function practicar (Tablero $tablero, Jugador $jugador){
        echo $tablero->mostrarTablero() . "\n\n";

        do{
            $partida = $tablero->mostrarTablero();
            $letra = $jugador->sugerirLetra($partida);
            $tablero->intentarLetra($letra);
            if($tablero->palabraAdivinada() == TRUE){
                return "El jugador gano, la palabra secreta era: " . $tablero->palabraSecretaPublica . "\n";
            }
        }while(1!=0);
    }
}

class Ahorcado implements Tablero{
  protected $palabrasDisponibles;
  protected $palabraSecreta;
  protected $letrasAdivinadas = [];
  public $palabraSecretaPublica;

  public function __construct() {
    $this->palabrasDisponibles = ['avestruz', 'horario', 'elefante', 'sopa'];
    $this->elegirNuevaPalabra();
  }

  public function elegirNuevaPalabra() {
    $this->letrasAdivinadas = [];
    shuffle($this->palabrasDisponibles);
    $this->palabraSecreta = $this->palabrasDisponibles[0];
    $this->palabraSecretaPublica = $this->palabraSecreta;
  }
  public function mostrarTablero() : string {
    $tablero = '';
    for ($x = 0; $x < strlen($this->palabraSecreta); $x++) {
      $letra = $this->palabraSecreta[$x];
      if (in_array($letra, $this->letrasAdivinadas)) {
        $tablero = $tablero . " $letra";
      }
      else {
        $tablero = $tablero . ' _';
      }
    }
    return $tablero;
  }

  public function palabraAdivinada() : bool {
    return substr_count($this->mostrarTablero(), '_') == 0;
  }

  public function intentarLetra(string $letra) : bool {
    if (strlen($letra) > 1) {
      throw Exception('Solo se aceptan letras, no palabras.');
    }
    $acierto = substr_count($this->palabraSecreta, $letra) != 0;
    if ($acierto) {
      $this->letrasAdivinadas[] = $letra;
    }
    return $acierto;
  }
}

class JugadorPrincipiante implements Jugador{

  public function sugerirLetra(string $tablero) : string {
    $letras = range('a', 'z');
    shuffle($letras);
    return $letras[0];
  }

}

class JugadorIntermedio implements Jugador{

    public function sugerirLetra(string $tablero) : string {
    $letras = range('a', 'z');
    do {
      shuffle($letras);
      $letra_elegida = $letras[0];
    } while ($this->letraYaAdivinada($letra_elegida, $tablero));
    return $letra_elegida;
  }

    protected function letraYaAdivinada(string $letra, string $tablero) : bool {
        return substr_count($tablero, $letra) > 0;
    }
}

class JugadorAvanzado implements Jugador{
    public $letrasDichas = "";

    public function sugerirLetra(string $tablero) : string {
        $letras = range('a', 'z');
        do {
            shuffle($letras);
            $letra_elegida = $letras[0];
        } while ($this->letraYaAdivinada($letra_elegida, $tablero) || $this->letraYaDicha($this->letrasDichas,$letra_elegida));
        return $letra_elegida;
    }

    protected function letraYaAdivinada(string $letra, string $tablero) : bool {
        return substr_count($tablero, $letra) > 0;
    }

    protected function letraYaDicha(string $letrasDichas, string $letra) : bool{
        if(substr_count($letrasDichas, $letra) > 0){
            return 1;
        }else{
            $this->letrasDichas = $this->letrasDichas . $letra;
            return 0;
        }
    }

    public function limpiarMemoria(){
        $this->letrasDichas = "";
    }
}

class partidas{
    public $oportunidades;
    public $esAvanzado;

    public function __construct(int $opor=50,int $esAvanzado=0){
        $this->oportunidades = $opor;
        $this->esAvanzado = $esAvanzado;
    }

    public function jugar(Jugador $jugador, int $partidas, int $esAvanzado){
        
        for($i = 0; $i < $partidas; $i++){
            $juego = new Juego();
            $tablero = new Ahorcado();
            print($juego->jugar($tablero, $jugador, $this->oportunidades));
            if($esAvanzado == 1){
                $jugador->limpiarMemoria();
            }
        }
    }
}

$jugador = new JugadorIntermedio();
$partidas = new partidas();
$partidas->jugar($jugador, 10);

/*

Nombre: Facundo
Apellido: Ferrari

Nota:

    Hola profesor, este es el programa de la prueba. La verdad es que ni me gaste en mirar las fotos de la prueba mia porque
siento que en el examen me puse nervioso y no podia pensar una estrategia para resolverlo. Por lo tanto tarde tanto que no pude
escribir un codigo que hiciera sentido en el papel. Ademas no probarlo en computadora es algo que tambien me pone un poco nervioso.
    En fin, Esta vez pude hacer el programa como yo queria. Espero que este bien y cumpla con la consigna. Se que hay cosas que no
pedia comop la clase partidas. Pero fue mi manera de resolver un poco mas formal el tema de poder hacer que haya varias jugadas y
de "limpiar" la memoria del jugador para que que vuelva a repetir las letras ya dichas pero en otra partida.
    Ademas lo que hice fue que le puedas indicar una cantidad de oportunidades que le das al jugador para poder resolver la palabra
o sino toma una por default con el construct. Tambien le agregue para que se pueda indicar una cantidad de partidas deseadas que
juegue el jugador. Tambien agregue que si le estas pasando por parametro un jugador avanzado, indicarle a la funcion con un 1 asi
se limpia la memoria. Por default es 0. Asi acepta jugadores principiantes e intermedios que son los que no tienen esta funcionalidad
de recordar las letras que ya fueron dichas.

No es para agrandarme ni nada similar porque no soy quien para hacerlo. Simplemente quiero mencionar que lo hice sin buscar nada en
Google. Esto lo digo porque nos pediste que te seamos sinceros de si buscamos en google soluciones o de si nos copiamos de alguien.
Pero la verdad es que no. Saludos.

*/