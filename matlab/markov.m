function [rating, ranking] = markov(S, beta) 
% ENTRADA
%   S matriz estocastica, suma de todas las matrices estocasticas S_i
%   beta parametro en caso de que S no sea irreducible
%
% SALIDA
%   rating vector columna con el rating de cada equipos
%   ranking vector columna con el ranking de cada equipo


% Tamanio de la matriz y numero de equipos
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
ranking = zeros(1,n);

rating_ordenado = sort(rating, 'descend');
for i=1:n 
    ranking(i) = find(rating_ordenado == rating(i),1);
end

ranking = ranking';
end