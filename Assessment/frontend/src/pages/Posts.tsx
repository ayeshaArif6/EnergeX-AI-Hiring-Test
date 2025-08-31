import { useEffect, useState, type FormEvent } from "react";
import { getPosts, createPost, updatePost, deletePost, type Post } from "../api";
import { useAuth } from "../AuthContext";

export default function Posts() {
  const { token, user, logout } = useAuth();
  const [posts, setPosts] = useState<Post[]>([]);
  const [title, setTitle] = useState("");
  const [content, setContent] = useState("");
  const [editingId, setEditingId] = useState<number | null>(null);
  const [editTitle, setEditTitle] = useState("");
  const [editContent, setEditContent] = useState("");
  const [err, setErr] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    try {
      const list = await getPosts();
      setPosts(list);
      setErr(null);
    } catch (e: any) {
      setErr(e?.response?.data?.error || "Load failed");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { load(); }, []);

  const onCreate = async (e: FormEvent) => {
    e.preventDefault();
    if (!token) { setErr("Login first"); return; }
    try {
      await createPost({ title, content });
      setTitle(""); setContent("");
      await load();
    } catch (e: any) {
      setErr(e?.response?.data?.error || "Create failed");
    }
  };

  const startEdit = (p: Post) => {
    setEditingId(p.id);
    setEditTitle(p.title);
    setEditContent(p.content);
  };

  const saveEdit = async (id: number) => {
    try {
      await updatePost(id, { title: editTitle, content: editContent });
      setEditingId(null);
      await load();
    } catch (e: any) {
      setErr(e?.response?.data?.error || "Update failed");
    }
  };

  const remove = async (id: number) => {
    if (!confirm("Delete this post?")) return;
    try { await deletePost(id); await load(); }
    catch (e:any){ setErr(e?.response?.data?.error || "Delete failed"); }
  };

  const mine = (p: Post) => user && p.user_id === user.id;

  return (
    <div>
      <div className="header">
        <h2>Posts</h2>
        <div>
          {user ? (<><span>Hi, {user.name}</span> <button onClick={logout}>Logout</button></>)
                : <span>Not logged in</span>}
        </div>
      </div>

      <form className="card" onSubmit={onCreate}>
        <h3>New Post</h3>
        {!token && <p className="muted">Login to create posts.</p>}
        <input placeholder="Title" value={title} onChange={e => setTitle(e.target.value)} />
        <textarea placeholder="Content" value={content} onChange={e => setContent(e.target.value)} />
        <button disabled={!token || !title || !content}>Create</button>
      </form>

      {err && <p className="error">{err}</p>}
      {loading && <p className="muted">Loading…</p>}
      {!loading && posts.length === 0 && <p className="muted">No posts yet.</p>}

      <ul className="list">
        {posts.map(p => (
          <li key={p.id}>
            {editingId === p.id ? (
              <div>
                <input value={editTitle} onChange={e=>setEditTitle(e.target.value)} />
                <textarea value={editContent} onChange={e=>setEditContent(e.target.value)} />
                <div style={{display:'flex', gap:8}}>
                  <button type="button" onClick={()=>saveEdit(p.id)}>Save</button>
                  <button type="button" onClick={()=>setEditingId(null)}>Cancel</button>
                </div>
              </div>
            ) : (
              <>
                <h4>{p.title}</h4>
                <p>{p.content}</p>
                <small>user #{p.user_id} {p.created_at ? "• "+p.created_at : ""}</small>
                {mine(p) && (
                  <div style={{marginTop:8, display:'flex', gap:8}}>
                    <button type="button" onClick={()=>startEdit(p)}>Edit</button>
                    <button type="button" onClick={()=>remove(p.id)}>Delete</button>
                  </div>
                )}
              </>
            )}
          </li>
        ))}
      </ul>
    </div>
  );
}
