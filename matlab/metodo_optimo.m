function [X, obj, r] = metodo_optimo(C) 
% ENTRADA
%   C matriz de conformidad
%
% SALIDA
%   X matriz de las variables de decision
%   obj valor de la funcion objetivo
%   r ranking producido por el metodo

% Tamanio de la matriz y numero de equipos
[m,n] = size(C);

% Funcion objetivo
f = -reshape(C,[1,n^2]);

% Vectores con las restricciones
b = 2*ones(n*(n-1)*(n-2),1);
beq = ones(n*(n-1),1);
lb = zeros(n^2,1);
ub = ones(n^2,1);

contador = 1;

% Matrices de restricciones de tipo 1 y tipo 2, respectivamente
T1 = zeros(n*(n-1),n^2);
T2 = zeros(n*(n-1)*(n-2),n^2);

% Restricciones de tipo 1
for i=1:n
    for j=1:n
        if i ~= j
            indice1 = sub2ind(size(C), i, j);
            indice2 = sub2ind(size(C), j, i);
            
            T1(contador,indice1) = 1;
            T1(contador,indice2) = 1;
            contador = contador + 1;
        end
    end
end

contador = 1;

% Restricciones de tipo 2

for i=1:n
    for j=1:n
        for k=1:n
            if (i ~= j) && (j ~= k) && (k ~= i)
                indice1 = sub2ind(size(C), i, j);
                indice2 = sub2ind(size(C), j, k);
                indice3 = sub2ind(size(C), k, i);

                T2(contador,indice1) = 1;
                T2(contador,indice2) = 1;
                T2(contador,indice3) = 1;                
                contador = contador + 1;
            end
        end
    end
end

% Todas las variables son enteras
intercon = 1:n;

% Resolvemos el problema
[X,fval] = intlinprog(f, intercon, T2, b, T1, beq, lb, ub);

% Valor de la funcion objetivo
obj = -fval; 

% Matriz con las varibles de decision
X = reshape(X',[n,n]);

% Creamos el ranking
ranking = zeros(1,n);

rating_ordenado = sort(rating, 'descend');
for i=1:n 
    ranking(i) = find(rating_ordenado == rating(i),1);
end

ranking = ranking';


end