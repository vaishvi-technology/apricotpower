export const FooterCategorySkeleton = () => {
  return (
    <ul className="footer-main-links-list">
      {[1, 2, 3, 4, 5,6,7,8,9,10].map((_, i) => (
        <li key={i} className="footer-main-links-item">
          <div className="skeleton skeleton-line"></div>
        </li>
      ))}
    </ul>
  );
};
