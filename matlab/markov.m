function [rating, ranking] = markov(S, beta) 
% ENTRADA
%   S matriz estoc�stica, suma de todas las matrices estoc�sicas S_i
%   beta par�metro en caso de que S no sea irreducib�e
%
% SALIDA
%   rating vector columna con el rating de cada equipos
%   ranking vector columna con el ranking de cada equipo


% Tama�o de la matriz y n�mero de equipos
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