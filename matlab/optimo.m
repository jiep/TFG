f1 = [0 -16 -14 -12 -12 16 14 14 -2 16 -14 -14];
f2 = [16 0 -12 -12 -12 16 16 16 14 16 12 0];
f3 = [14 12 0 -12 -12 16 16 16 16 16 12 12];
f4 = [12 12 12 0 -14 14 12 12 12 14 12 12];
f5 = [12 12 12 14 0 16 12 12 12 12 12 12];
f6 = [-16 -16 -16 -14 -16 0 12 12 -16 12 -16 -16];
f7 = [-14 -16 -16 -12 -12 -12 0 -12 -14 6 -12 -14];
f8 = [-14 -16 -16 -12 -12 -12 12 0 -14 16 -12 -14];
f9 = [2 -14 -16 -12 -12 16 14 14 0 16 -12 -16];
f10= [-16 -16 -16 -14 -12 -12 -6 -16 -16 0 -16 -16];
f11= [14 -12 -12 -12 -12 16 12 12 12 16 0 -8];
f12= [14 0 -12 -12 -12 16 14 14 16 16 8 0];

M = [f1; f2; f3; f4; f5; f6; f7; f8; f9; f10; f11; f12];

n=12;

%M = [0 -3 -3 -3 -3; 3 0 -3 -1 -1; 3 3 0 3 -1; 3 1 -3 0 -3; 3 1 1 3 0]

%Matriz de prueba
%M = zeros(n,n);
% Funcion objetivo
f = -reshape(M,[1,n^2]);

b = 2*ones(n*(n-1)*(n-2),1);
beq = ones(n*(n-1),1);
lb = zeros(n^2,1);
ub = ones(n^2,1);

contador = 1;
T1 = zeros(n*(n-1),n^2);
T2 = zeros(n*(n-1)*(n-2),n^2);

for i=1:n
    for j=1:n
        if i ~= j
            indice1 = sub2ind(size(M), i, j);
            indice2 = sub2ind(size(M), j, i);
            
            T1(contador,indice1) = 1;
            T1(contador,indice2) = 1;
            contador = contador + 1;
        end
    end
end

contador = 1;

for i=1:n
    for j=1:n
        for k=1:n
            if (i ~= j) && (j ~= k) && (k ~= i)
                indice1 = sub2ind(size(M), i, j);
                indice2 = sub2ind(size(M), j, k);
                indice3 = sub2ind(size(M), k, i);

                T2(contador,indice1) = 1;
                T2(contador,indice2) = 1;
                T2(contador,indice3) = 1;                
                contador = contador + 1;
            end
        end
    end
end
intercon = 1:n;
%options = optimoptions('TolInteger',1e-10);

%[x,fval,exitflag,output] = intlinprog(f, intercon, T2, b, T1, beq, lb, ub)
[x,fval,exitflag,output] = linprog(f, T2, b, T1, beq, lb, ub)

