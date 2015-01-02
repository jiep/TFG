function [rating, ranking] = colley(C, b) 
% ENTRADA
%   C matriz de Colley
%   b vector del lado derecho del sistema de Colley
%
% SALIDA
%   rating vector columna con el rating de cada equipos
%   ranking vector columna con el ranking de cada equipo

% Tamanio de la matriz y numero de equipos
[m,n] = size(C);

% Calculamos el rating
rating = C\b;

% Creamos el ranking
a = [[1:n]' rating];
[E,ranking] = sortrows(a, -2);

end
