function ro = spearman(c_1, c_2) 
% ENTRADA
%   c_1 ranking completo con m elementos
%   c_2 ranking completo con m elementos
%
% SALIDA
%   ro valor de la ro de Spearman de los rankings c_1 y c_2

[m,n] = size(c_1);
ro = 0;

for i=1:length(c_1)
    posicion_c1 = find(c_1 == c_1(i));
    posicion_c2 = find(c_2 == c_1(i));
    ro = ro + abs(posicion_c1 - posicion_c2);

end

end
