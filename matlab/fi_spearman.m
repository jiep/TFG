function fi = fi_spearman(c_1, c_2) 
% ENTRADA
%   c_1 ranking completo con m elementos
%   c_2 ranking completo con m elementos
%
% SALIDA
%   ro valor de la fi de Spearman de los rankings c_1 y c_2

[m,n] = size(c_1);
fi = 0;

for i=1:m
    fi = fi + abs(c_1(i) - c_2(i))/min(c_1(i),c_2(i));
end

end
