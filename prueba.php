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
    public function jugar(string $nombre,Tablero $tablero, Jugador $jugador, int $intentos){

        echo $tablero->mostrarTablero() . "\n\n";

        do{
            $partida = $tablero->mostrarTablero();
            $letra = $jugador->sugerirLetra($partida);
            $tablero->intentarLetra($letra);
            if($tablero->palabraAdivinada() == TRUE){
                return $nombre . " gano, la palabra secreta era: " . $tablero->palabraSecretaPublica . "\n";
            }
            $intentos--;
        }while($intentos != -1);

        return $nombre . " perdio, la palabra secreta era: " . $tablero->palabraSecretaPublica  . "\n";
    }

    public function practicar (Tablero $tablero, Jugador $jugador){
        echo $tablero->mostrarTablero() . "\n\n";

        do{
            $partida = $tablero->mostrarTablero();
            $letra = $jugador->sugerirLetra($partida);
            $tablero->intentarLetra($letra);
            if($tablero->palabraAdivinada() == TRUE){
                return $nombre . " gano, la palabra secreta era: " . $tablero->palabraSecretaPublica . "\n";
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
    public $jugador;

    public function __construct(int $opor=50,int $esAvanzado=0){
        $this->oportunidades = $opor;
        $this->esAvanzado = $esAvanzado;
    }

    public function jugar(string $nombre,int $partidas, int $esAvanzado,int $tipoJugador){
        
        if($tipoJugador == 1){
            $this->jugador = new JugadorPrincipiante();
        }elseif($tipoJugador == 2){
            $this->jugador = new JugadorIntermedio();
        }elseif($tipoJugador == 3){
            $this->jugador = new JugadorAvanzado();
        }
        for($i = 0; $i < $partidas; $i++){
            $juego = new Juego();
            $tablero = new Ahorcado();
            print($juego->jugar($nombre,$tablero, $this->jugador, $this->oportunidades));
            if($esAvanzado == 1){
                $this->jugador->limpiarMemoria();
            }
        }
    }
}

$partidas = new partidas();
$partidas->jugar("Juan",10,1,3);

/*
Nombre: Facundo
Apellido: Ferrari

V2.0 --> Ahora se puede crear un jugador distinto dependiendo de el input que le demos a la funcion Jugar. Un input que va del 1 al 3 dependiendo de el tipo de jugador que quiera crear. Por lo tanto le corresponde el 1 al tipo de jugador Principiante, el 2 al jugador Intermedio y el 3 al jugador Avanzado. Esta nueva funcionalidad se implemento para una mejor experiencia de juego. Asi no se tendra que crear una instancia nueva para cada tipo de jugador.
*/
