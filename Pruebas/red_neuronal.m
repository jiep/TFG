% Leemos datos de entrada
entradas = importdata('entradas.txt');

% Leemos los datos de salida
salidas = importdata('salidas.txt');

% Trasponemos los datos
entradas = entradas';
salidas = salidas';

% Creaci�n de la red neuronal con 10 entradas, 100 neuronas en la capa
% oculta y 8 salidas
goles = newff([0 1; 0 1; 0 1; 0 1; 0 1; 0 1; 0 1; 0 1; 0 1; 0 1],[100 8],{'poslin','logsig'});

% Entrenamos a la red neuronal con las entradas y las salidas
goles = train(goles,entradas,salidas);