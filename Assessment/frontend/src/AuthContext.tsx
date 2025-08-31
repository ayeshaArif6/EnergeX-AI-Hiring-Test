import React, {createContext, useContext, useState} from "react";
import type { User } from "./api";

type AuthState = { token:string|null; user:User|null };
type Ctx = AuthState & { setAuth:(s:AuthState)=>void; logout:()=>void };
const Auth = createContext<Ctx|null>(null);

export const AuthProvider: React.FC<{children:React.ReactNode}> = ({children}) => {
  const [state, setState] = useState<AuthState>(() => {
    const t = localStorage.getItem("token");
    const u = localStorage.getItem("user");
    return { token: t, user: u ? JSON.parse(u) : null };
  });
  const setAuth = (s:AuthState) => {
    setState(s);
    s.token ? localStorage.setItem("token", s.token) : localStorage.removeItem("token");
    s.user  ? localStorage.setItem("user", JSON.stringify(s.user)) : localStorage.removeItem("user");
  };
  const logout = () => setAuth({token:null, user:null});
  return <Auth.Provider value={{...state, setAuth, logout}}>{children}</Auth.Provider>;
};
export const useAuth = () => {
  const v = useContext(Auth);
  if (!v) throw new Error("useAuth outside provider");
  return v;
};
