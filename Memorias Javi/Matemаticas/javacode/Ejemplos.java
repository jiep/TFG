import java.util.ArrayList;

//En nuestros ejemplos los rankings son de 18 equipos
private static final int MAX = 18;
    
//Los rankings se almacenaran en ArrayList

//Lista de equipos
ArrayList<String> equipos = new ArrayList<String> ();
equipos.add("Sev");equipos.add("Zar");equipos.add("Bil");
equipos.add("Bar");equipos.add("Jov");equipos.add("Gip");
equipos.add("GC");equipos.add("Ten");equipos.add("Bru");
equipos.add("Bas");equipos.add("Fue");equipos.add("And");
equipos.add("Est");equipos.add("Mad");equipos.add("Rio");
equipos.add("Mur");equipos.add("Mal");equipos.add("Val");

//Massey
ArrayList<String> r1 = new ArrayList<String> (); 
r1.add("Bar");r1.add("Mad");r1.add("Bas");r1.add("Val");
r1.add("Mal");r1.add("Bil");r1.add("Ten");r1.add("Jov");
r1.add("GC");r1.add("Mur");r1.add("And");r1.add("Zar");
r1.add("Rio");r1.add("Est");r1.add("Fue");r1.add("Sev");
r1.add("Bru");r1.add("Gip");

//Keener
ArrayList<String> r2 = new ArrayList<String> (); 
r2.add("Mad");r2.add("Bar");r2.add("Mal");r2.add("Bil");
r2.add("Bas");r2.add("Zar");r2.add("Jov");r2.add("Val");
r2.add("GC");r2.add("Mur");r2.add("Rio");r2.add("Ten");
r2.add("Est");r2.add("Sev");r2.add("And");r2.add("Bru");
r2.add("Gip");r2.add("Fue");

//Ataque-Defensa
ArrayList<String> r3 = new ArrayList<String> ();   
r3.add("Bar");r3.add("Mad");r3.add("Bas");r3.add("Mal");
r3.add("Val");r3.add("Bil");r3.add("Ten");r3.add("Jov");
r3.add("GC");r3.add("Mur");r3.add("Zar");r3.add("Rio");
r3.add("And");r3.add("Est");r3.add("Fue");r3.add("Sev");
r3.add("Bru");r3.add("Gip");
        