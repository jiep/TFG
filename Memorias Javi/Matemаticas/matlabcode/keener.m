function [r] = keener(A,x)

    %  Parametros de Entrada:
    %  A matriz de Keener
    %  La primera vez que realicemos el proceso x es el vector 
    %  x0=[1/m,1/m,...,1/m]
    %  En calculos posteriores x es el vector obtenido en el    
    %  calculo anterior
    %
    %  Parametros de salida
    %  r vector de ratings

    num=A*x;
    den=sum(num);
    r = num/den;

end