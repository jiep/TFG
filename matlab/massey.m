function r = massey(n,filename)
% ENTRADA
%   n número de equipos totales
%   filename nombre del archivo csv con la siguiente estructura:
%        equipo local, marcador local, equipo visitante, marcador visitante
%
% SALIDA
%   r vector rating producido por el método de Massey

M = -ones(n,n);

for i=1:n
    M(n,i) = 1;
    M(i,i) = 0;
end

N = zeros(n,n);
teams = zeros(n);

file = importdata(filename);
size = length(file{1}{1});

for i=1:n
    
end




M = -ones(n,n);

end
