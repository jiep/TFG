function [rating, ranking] = markov(S, beta) 
% ENTRADA
%   S matriz estocástica, suma de todas las matrices estocásicas S_i
%   beta parámetro en caso de que S no sea irreducibñe
%
% SALIDA
%   rating vector columna con el rating de cada equipos
%   ranking vector columna con el ranking de cada equipo


% Tamaño de la matriz y número de equipos
[m,n] = size(S);

% Calculamos los autovalores de S
[V D] = eig(S.');
rating = V(:,1).';
rating = rating./sum(rating);
rating = rating';

if any(rating) == zeros(n) % No es irreducible
    E = ones(n,n);
    S_ = beta*S + (1-beta)/n*E;
    [V D] = eig(S_.');
    rating = V(:,1).';
    rating = rating./sum(rating);
    rating = rating'
else
    [V D] = eig(S.');
    rating = V(:,1).';
    rating = rating./sum(rating);
    rating = rating'
end

% Creamos el ranking
a = [[1:n]' rating];
[E,ranking] = sortrows(a, -2);
end