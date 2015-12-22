function [d] = defensa(A,o)

    %  Parametros de Entrada:
    %  A matriz de puntuaciones
    %  o es el vector defensa obtenido en k 
    %
    %  Parametros de salida
    %  d vector de defensa
    
    % Calculamos el vector de inversos de 0
    for i=1:18
        oinv(i) = 1/o(i)
    end
    
    d = A*(oinv');

end