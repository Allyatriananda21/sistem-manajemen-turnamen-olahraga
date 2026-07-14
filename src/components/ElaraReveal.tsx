import { useEffect, useRef } from "react";
import { gsap } from "gsap";
import { CustomEase } from "gsap/CustomEase";
import { SplitText } from "gsap/SplitText";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import img from "../assets/image.png";
import "./ElaraReveal.css";

gsap.registerPlugin(CustomEase, SplitText, ScrollTrigger);

export default function ElaraReveal() {
  const rootRef = useRef<HTMLDivElement>(null);
  const playedRef = useRef(false);

  useEffect(() => {
    const root = rootRef.current;
    if (!root) return;

    // Guard StrictMode double-mount: only play the reveal once.
    if (playedRef.current) return;

    let split: SplitText | null = null;

    const ctx = gsap.context(() => {
      CustomEase.create("hop", "0.85, 0, 0.15, 1");

      const build = () => {
        if (playedRef.current) return;
        playedRef.current = true;

        split = SplitText.create(".hero-header h1", {
          type: "words",
          mask: "words",
          wordsClass: "word",
        });





        // revealTl
        const revealTl = gsap.timeline({ delay: 0.5 });
        revealTl
          .to(".hero-images .img", {
            y: 0,
            opacity: 1,
            stagger: 0.05,
            duration: 1.5,
            ease: "hop",
          })
          .to(
            ".hero-images",
            { gap: "0.75vw", duration: 1.5, ease: "hop" },
            "+=0.5",
          )
          .to(
            ".hero-images .img",
            { scale: 1, duration: 1.5, ease: "hop" },
            "<",
          )
          .to(".hero-images .img:not(.hero-img)", {
            clipPath: "polygon(0% 0%, 100% 0%, 100% 0%, 0% 0%)",
            stagger: 0.1,
            duration: 1.5,
            ease: "hop",
          })
          .to(".hero-img", { scale: 1.5, duration: 1.5, ease: "hop" })

          .to(
            ".hero-header h1 .word",
            { y: "0%", stagger: 0.1, duration: 1.5, ease: "hop" },
            "-=0.5",
          );
      };

      // Trigger when the section scrolls into view (not on mount).
      ScrollTrigger.create({
        trigger: root,
        start: "top 60%",
        once: true,
        onEnter: () => {
          // Split after fonts + layout are ready for correct word measurement.
          document.fonts.ready.then(build);
        },
      });

      // Pin the section so the image stays on screen without fading out
      ScrollTrigger.create({
        trigger: root,
        start: "top top",
        end: "+=120%", // Keep it pinned for 120% of viewport height scroll
        pin: true, // This makes it "bertahan" (persist/pin) on screen
      });
    }, rootRef);

    return () => {
      ctx.revert();
      split?.revert();
    };
  }, []);

  return (
    <div className="elara-reveal" ref={rootRef}>
      <section className="hero">

        <div className="hero-images">
          <div className="img">
            <img src={img} alt="" />
          </div>
          <div className="img">
            <img src={img} alt="" />
          </div>
          <div className="img hero-img">
            <img src={img} alt="" />
          </div>
          <div className="img">
            <img src={img} alt="" />
          </div>
          <div className="img">
            <img src={img} alt="" />
          </div>
        </div>
        <div className="hero-header">
          <h1>KEJUARAAN OLAHRAGA</h1>
        </div>
      </section>
    </div>
  );
}
