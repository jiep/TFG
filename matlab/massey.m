function [rating, ranking] = massey(M, p) 
% ENTRADA
%   M matriz de Massey
%   p vector del lado derecho del sistema de Massey
%
% SALIDA
%   rating vector columna con el rating de cada equipos
%   ranking vector columna con el ranking de cada equipo

% Tamaño de la matriz y número de equipos
[m,n] = size(M);

% Calculamos el rating
rating = M\p;

% Creamos el ranking
ranking = zeros(1,n);

rating_ordenado = sort(rating, 'descend');
for i=1:n 
    ranking(i) = find(rating_ordenado == rating(i),1);
end

ranking = ranking';

end
