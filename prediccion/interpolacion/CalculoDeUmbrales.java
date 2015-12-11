import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.io.FileWriter;
import java.io.IOException;

public class CalculoDeUmbrales {

   public static final int TEMP_INI = 1990;//Jornada de inicio (antes de 1990 no había 20 equipos ni 38 jornadas)
   public static final int TEMP_FIN = 2014;//Jornada de actual
   public static final int JORNADA = 37;//Calcula las probabilidades hasta la jornada (Max 37)
   public static Connection conexion;

    public static void main(String[] args) throws ClassNotFoundException, SQLException {

        ArrayList<String> r = new ArrayList<String> ();
        String local, visitante;
        int gl,gv,posl,posv;
        double c1vs20_1=0; double c1vs20_X=0; double c1vs20_2=0;//Contadores cuando se enfrentan el primero contra ultimo
        double c20vs1_1=0; double c20vs1_X=0; double c20vs1_2=0;//Contadores cuando se enfrentan el ultimo contra el primero
        double cons_1=0; double cons_X=0; double cons_2=0;//Contadores cuando se enfrentan equipos consecutivos
        int n = 1;//Contador de jornada
        double p1vs20_1, p1vs20_X, p1vs20_2;//Probabilidades cuando se enfrentan el primero contra ultimo
        double p20vs1_1, p20vs1_X, p20vs1_2;//Probabilidades cuando se enfrentan el ultimo contra el primero
        double pcons_1, pcons_X, pcons_2;//Probabilidades cuando se enfrentan equipos consecutivos

        //Inicializa el Driver
        Class.forName("com.mysql.jdbc.Driver");


        //Conexión a base de datos
        try {
            conexion = DriverManager.getConnection("jdbc:mysql://localhost:3306/rankings","rankings","1234");
        } catch (SQLException ex) {
            Logger.getLogger(CalculoDeUmbrales.class.getName()).log(Level.SEVERE, null, ex);
        }

        //Creamos el statement para hacer consultas
        Statement st = conexion.createStatement();



        int t = TEMP_INI;//Temporada desde la que calcularemos los resultados

        while(t<=TEMP_FIN){

                while(n<JORNADA){

                    //Ranking
                    ResultSet rs = st.executeQuery("SELECT equipo FROM rankings WHERE temporada=\""+t+"/"+(t+1)+"\" AND jornada="+n);
                    while (rs.next())
                    {
                       r.add(rs.getObject("equipo").toString()); //Almacenamos el rankings en un arraylist
                    }
                    rs.close();


                    //Partidos
                    ResultSet rs2 = st.executeQuery("SELECT equipo_local,equipo_visitante,goles_local,goles_visitante FROM partidos WHERE temporada=\""+t+"/"+(t+1)+"\" AND jornada="+(n+1));
                    while (rs2.next())
                    {
                       //Obtenemos nombres de los equipos
                       local = rs2.getObject("equipo_local").toString();
                       visitante = rs2.getObject("equipo_visitante").toString();

                       //Obtenemos posición de los equipos
                       posl = r.indexOf(local)+1;
                       posv = r.indexOf(visitante)+1;

                       //Obtenemos goles de los equipos
                       gl = rs2.getInt("goles_local");
                       gv = rs2.getInt("goles_visitante");


                       if(posl-posv==-19){
                           //System.out.println("1 vs 20: temporada "+t+" jornada "+n+" entre "+local+" "+visitante);
                           if(gl>gv){c1vs20_1++;}
                           else if (gl==gv) {c1vs20_X++;}
                           else{c1vs20_2++;}
                       }else if(posl-posv==19){
                           //System.out.println("20 vs 1: temporada "+t+" jornada "+n+" entre "+local+" "+visitante);
                           if(gl>gv){c20vs1_1++;}
                           else if (gl==gv) {c20vs1_X++;}
                           else{c20vs1_2++;}
                       }else if(posl-posv==1){
                           if(gl>gv){cons_1++;}
                           else if (gl==gv) {cons_X++;}
                           else{cons_2++;}
                       }

                    }
                    rs2.close();

                    r.clear();//Vaciamos el arraylist
                    n++;
                }

        n=1;//Comenzamos de nuevo en la jornada 1
        t++;
        }



        //Cerramos
        st.close();
        conexion.close();

        //Calculamos las probabilidades
        p1vs20_1 = c1vs20_1/(c1vs20_1+c1vs20_X+c1vs20_2);
        p1vs20_X = c1vs20_X/(c1vs20_1+c1vs20_X+c1vs20_2);
        p1vs20_2 = c1vs20_2/(c1vs20_1+c1vs20_X+c1vs20_2);

        p20vs1_1 = c20vs1_1/(c20vs1_1+c20vs1_X+c20vs1_2);
        p20vs1_X = c20vs1_X/(c20vs1_1+c20vs1_X+c20vs1_2);
        p20vs1_2 = c20vs1_2/(c20vs1_1+c20vs1_X+c20vs1_2);

        pcons_1 = cons_1/(cons_1+cons_X+cons_2);
        pcons_X = cons_X/(cons_1+cons_X+cons_2);
        pcons_2 = cons_2/(cons_1+cons_X+cons_2);

      	try{
      		/* Devolver csv de valores
      		FileWriter writer = new FileWriter("umbrales.csv");

      		writer.append(Double.toString(p1vs20_1));
      		writer.append(',');
      		writer.append(Double.toString(p1vs20_X));
      		writer.append(',');
      		writer.append(Double.toString(p1vs20_2));
      		writer.append('\n');

      		writer.append(Double.toString(pcons_1));
      		writer.append(',');
      		writer.append(Double.toString(pcons_X));
      		writer.append(',');
      		writer.append(Double.toString(pcons_2));
      		writer.append('\n');

      		writer.append(Double.toString(p20vs1_1));
      		writer.append(',');
      		writer.append(Double.toString(p20vs1_X));
      		writer.append(',');
      		writer.append(Double.toString(p20vs1_2));
      		writer.append('\n');*/

      		FileWriter writer = new FileWriter("output.txt");

      		writer.append("define(\"X1\",");
      		writer.append(Double.toString(p1vs20_1));
      		writer.append(");\n");

      		writer.append("define(\"Y1\",");
      		writer.append(Double.toString(pcons_1));
      		writer.append(");\n");

      		writer.append("define(\"Z1\",");
      		writer.append(Double.toString(p20vs1_1));
      		writer.append(");\n");

      		writer.append("define(\"X2\",");
      		writer.append(Double.toString(p1vs20_1+p1vs20_X));
      		writer.append(");\n");

      		writer.append("define(\"Y2\",");
      		writer.append(Double.toString(pcons_1+pcons_X));
      		writer.append(");\n");

      		writer.append("define(\"Z2\",");
      		writer.append(Double.toString(p20vs1_1+p20vs1_X));
      		writer.append(");\n");

      		writer.flush();
      		writer.close();

      	}catch(IOException e){
      		     e.printStackTrace();
        }

    }
}
