function tau = tau_kendall(c_1, c_2)
% ENTRADA
%   c_1 ranking con m elementos
%   c_2 ranking con m elementos
%
% SALIDA
%   tau valor de la tau de Kendall de los rankings c_1 y c_2

unicos = unique(cat(1,c_1,c_2));

n = length(unicos);
n_c = 0;
n_d = 0;
n_u = 0;

for i=2:n
    for j=1:i-1
        posicion_c1_i = find(c_1 == unicos(i),1);
        posicion_c1_j = find(c_1 == unicos(j),1);
        posicion_c2_i = find(c_2 == unicos(i),1);
        posicion_c2_j = find(c_2 == unicos(j),1);
            
        if (isempty(posicion_c1_i) && isempty(posicion_c1_j)) && (~isempty(posicion_c2_i) && ~isempty(posicion_c2_j)) || (~isempty(posicion_c1_i) && ~isempty(posicion_c1_j)) && (isempty(posicion_c2_i) && isempty(posicion_c2_j))
            n_u = n_u + 1;
        
        else
            a = abs(posicion_c1_i - posicion_c2_i);
            b = abs(posicion_c1_j - posicion_c2_j);
            if (posicion_c1_i >= posicion_c1_j) && (posicion_c2_i >= posicion_c2_j)
                n_c = n_c + 1;
            else
                n_d = n_d + 1;
            end
        end
        

    end
end

tau = (n_c - n_d - n_u)/(n*(n-1)/2 - n_u);   

n_c
n_d
n_u

end