function [rating, ranking] = massey(M, p) 
% ENTRADA
%   M matriz de Massey
%   p vector del lado derecho del sistema de Massey
%
% SALIDA
%   rating vector columna con el rating de cada equipos
%   ranking vector columna con el ranking de cada equipo

% Tamanio de la matriz y numero de equipos
[m,n] = size(M);

% Calculamos el rating
rating = M\p;

% Creamos el ranking
a = [[1:n]' rating];
[E,ranking] = sortrows(a, -2);

end
