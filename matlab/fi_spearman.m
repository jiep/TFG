function fi = fi_spearman(c_1, c_2) 
% ENTRADA
%   c_1 ranking con m elementos
%   c_2 ranking con m elementos
%
% SALIDA
%   ro valor de la fi de Spearman de los rankings c_1 y c_2

[m,n] = size(c_1);
fi = 0;

arm = harmonic(m);

x = (m - 4*floor(m/2) + 2*(m+1)*harmonic(floor(m/2)))/arm;

unicos = unique(cat(1,c_1,c_2));

phi_l = -2*m + 2*x*arm;

for i=1:length(unicos)
    posicion_c1 = find(c_1 == unicos(i));
    posicion_c2 = find(c_2 == unicos(i));
    if(isempty(posicion_c1) || isempty(posicion_c2)); 
        if(isempty(posicion_c1))
            fi = fi + abs(posicion_c2-x)/min(posicion_c2,x);
        else 
            fi = fi + abs(posicion_c1-x)/min(posicion_c1,x);
        end
    else
        fi = fi + abs(posicion_c1 - posicion_c2)/min(posicion_c1,posicion_c2);
    end
end

fi = fi/phi_l;

end
