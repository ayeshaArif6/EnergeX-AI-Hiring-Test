import axios, { AxiosHeaders } from "axios";

export type User = { id: number; name: string; email: string };
export type Post = { id: number; title: string; content: string; user_id: number; created_at?: string };

const BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000";

export const http = axios.create({
  baseURL: BASE,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});


http.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
 
    if (config.headers && typeof (config.headers as AxiosHeaders).set === "function") {
      (config.headers as AxiosHeaders).set("Authorization", `Bearer ${token}`);
    } else {
      config.headers = {
        ...(config.headers || {}),
        Authorization: `Bearer ${token}`,
      } as any;
    }
  }
  return config;
});


export const register = (name: string, email: string, password: string) =>
  http.post("/api/register", { name, email, password })
      .then((r) => r.data as { id: number });

export const login = (email: string, password: string) =>
  http.post("/api/login", { email, password })
      .then((r) => r.data as { token: string; user: User });


export async function getPosts(): Promise<Post[]> {
  const { data } = await http.get("/api/posts");
  return Array.isArray(data) ? data : (Array.isArray((data as any)?.posts) ? (data as any).posts : []);
}

export async function createPost(body: { title: string; content: string }) {
  
  const { data } = await http.post("/api/posts", body);
  return data as Post;
}

export async function updatePost(id: number, body: { title?: string; content?: string }) {
  const { data } = await http.put(`/api/posts/${id}`, body);
  return data as Post;
}

export async function deletePost(id: number) {
  const { data } = await http.delete(`/api/posts/${id}`);
  return data as { ok: boolean };
}
