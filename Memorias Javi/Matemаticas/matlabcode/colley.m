function [r] = colley(C,b)

    %  Parametros de Entrada:
    %  C matriz de Colley
    %  b vector definido por b=1+(1/2)(victorias-derrotas)
    %
    %  Parametros de salida
    %  r vector de ratings

    r = C\b;

end