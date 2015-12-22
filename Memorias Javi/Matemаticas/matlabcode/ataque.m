function [o] = ataque(A,d)

    %  Parametros de Entrada:
    %  A matriz de puntuaciones
    %  Primer calculo d0=[1,1,...,1]
    %  Calculos posteriores d vector defensa obtenido en k-1    
    %
    %  Parametros de salida
    %  o vector de ataque

    % Calculamos la transpuesta de A
    At = A';
    
    % Calculamos el vector de inversos de d
    for i=1:18
        dinv(i) = 1/d(i)
    end
    
    o = At*(dinv');

end

