function [r] = massey (M, p)

    %  Parametros de Entrada:
    %  M matriz de Massey
    %  p vector con la suma de las diferencias de puntos
    %
    %  Parametros de salida
    %  r vector de ratings

    r = M\p;

end