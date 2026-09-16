# Research

Reference material gathered while designing the theme. Nothing in here ships.

## `pens/`

Source from CodePen pens used as reference, one folder per pen
(`<author>-<id>/`), each containing `pen.html`, `pen.css`, `pen.js` and a
`SOURCE.txt` recording where it came from and when.

They live in the repo because the environment Claude runs in blocks
`codepen.io` at the network proxy — committing the source is the only reliable
way to get a pen in front of it.

### Adding one

```bash
./research/fetch-pen.sh https://codepen.io/author/pen/ID
```

Several at once is fine:

```bash
./research/fetch-pen.sh URL1 URL2 URL3
```

The script appends `.html` / `.css` / `.js` to the pen URL, which is how
CodePen serves raw panel source for public pens. It refuses to save anything
that comes back as an error or login page, so an empty folder means the trick
didn't work — in that case open the pen and copy the three panels by hand into
the same folder.

Then commit and push so the reference is available on the branch.

### A note on reuse

CodePen's default licence is MIT unless a pen states otherwise, so reading
these and learning the technique is entirely normal practice. Lifting a pen's
code verbatim into a theme that gets sold is a different question — keep the
attribution in `SOURCE.txt`, and check any pen whose code ends up substantially
intact in shipped work.

What we're actually after here is *mechanism*, not code: how a thing is built,
so it can be rebuilt in this project's own material language.
