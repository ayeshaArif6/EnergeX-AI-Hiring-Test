import axios from "axios";

export type User = { id: number; name: string; email: string };

const BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000";

export const http = axios.create({
  baseURL: BASE,
  headers: { "Content-Type": "application/json" },
});

export const register = (name: string, email: string, password: string) =>
  http
    .post("/api/register", { name, email, password })
    .then((r) => r.data as { id: number });

export const login = (email: string, password: string) =>
  http
    .post("/api/login", { email, password })
    .then((r) => r.data as { token: string; user: User });

export async function getPosts() {
  const { data } = await http.get("/api/posts");
  
  return Array.isArray(data) ? data : (Array.isArray((data as any)?.posts) ? (data as any).posts : []);
}

export async function createPost(
  token: string,
  body: { title: string; content: string }
) {
  const { data } = await http.post("/api/posts", body, {
    headers: { Authorization: `Bearer ${token}` },
  });
  return data;
}
