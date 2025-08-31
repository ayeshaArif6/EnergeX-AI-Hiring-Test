import { useEffect, useState } from "react";
import type { FormEvent } from "react";
import { getPosts, createPost } from "../api";
import { useAuth } from "../AuthContext";

type Post = { id:number; title:string; content:string; user_id:number; created_at?:string };

export default function Posts() {
  const { token, user, logout } = useAuth();
  const [posts,setPosts]=useState<Post[]>([]);
  const [title,setTitle]=useState(""); const [content,setContent]=useState("");
  const [err,setErr]=useState<string|null>(null);

  const load = async () => {
    try { setPosts(await getPosts()); } catch(e:any){ setErr(e?.response?.data?.error || "Load failed"); }
  };
  useEffect(()=>{ load(); },[]);

  const onCreate = async (e:FormEvent) => {
    e.preventDefault(); setErr(null);
    try { await createPost(token!, {title,content}); setTitle(""); setContent(""); await load(); }
    catch(e:any){ setErr(e?.response?.data?.error || "Create failed"); }
  };

  return (
    <div>
      <div className="header">
        <h2>Posts</h2>
        <div>{user ? (<><span>Hi, {user.name}</span> <button onClick={logout}>Logout</button></>) : <span>Not logged in</span>}</div>
      </div>

      <form className="card" onSubmit={onCreate}>
        <h3>New Post</h3>
        <input placeholder="Title" value={title} onChange={e=>setTitle(e.target.value)} />
        <textarea placeholder="Content" value={content} onChange={e=>setContent(e.target.value)} />
        <button disabled={!token}>Create (requires login)</button>
      </form>

      {err && <p className="error">{err}</p>}

      <ul className="list">
        {posts.map(p=>(
          <li key={p.id}>
            <h4>{p.title}</h4>
            <p>{p.content}</p>
            <small>user #{p.user_id} {p.created_at ? "• "+p.created_at : ""}</small>
          </li>
        ))}
      </ul>
    </div>
  );
}
