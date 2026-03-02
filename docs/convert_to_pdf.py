"""
Markdown to PDF Converter for Tequila App Documentation
This script converts all .md files in the docs folder to PDF format.

Requirements:
    pip install markdown2 pdfkit
    
    For pdfkit, you also need wkhtmltopdf:
    - Windows: Download from https://wkhtmltopdf.org/downloads.html
    - Or use the simpler markdown2pdf below
"""

import os
import sys

def convert_with_markdown2pdf():
    """Simple conversion using markdown library and reportlab"""
    try:
        from markdown2 import Markdown
        from reportlab.lib.pagesizes import letter
        from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer
        from reportlab.lib.styles import getSampleStyleSheet
        from reportlab.lib.units import inch
        
        print("✓ Found required libraries")
    except ImportError:
        print("Installing required packages...")
        os.system(f"{sys.executable} -m pip install markdown2 reportlab")
        from markdown2 import Markdown
        from reportlab.lib.pagesizes import letter
        from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer
        from reportlab.lib.styles import getSampleStyleSheet
        from reportlab.lib.units import inch
    
    # Get current directory
    docs_dir = os.path.dirname(os.path.abspath(__file__))
    
    # Files to convert
    files = [
        'USER_MANUAL.md',
        'TECHNICAL_DOCUMENTATION.md',
        'DEVELOPER_GUIDE.md'
    ]
    
    markdowner = Markdown()
    
    for md_file in files:
        md_path = os.path.join(docs_dir, md_file)
        pdf_path = os.path.join(docs_dir, md_file.replace('.md', '.pdf'))
        
        if not os.path.exists(md_path):
            print(f"✗ File not found: {md_file}")
            continue
        
        print(f"Converting {md_file}...")
        
        try:
            # Read markdown
            with open(md_path, 'r', encoding='utf-8') as f:
                md_content = f.read()
            
            # Convert to HTML
            html_content = markdowner.convert(md_content)
            
            # Create PDF
            doc = SimpleDocTemplate(pdf_path, pagesize=letter)
            styles = getSampleStyleSheet()
            story = []
            
            # Simple text conversion (basic)
            # Note: This is a simplified version. For full markdown support,
            # consider using pandoc or an online converter
            
            lines = md_content.split('\n')
            for line in lines:
                if line.strip():
                    if line.startswith('#'):
                        story.append(Paragraph(line.replace('#', '').strip(), styles['Heading1']))
                    else:
                        story.append(Paragraph(line, styles['Normal']))
                    story.append(Spacer(1, 0.1 * inch))
            
            doc.build(story)
            print(f"✓ Created: {pdf_path}")
            
        except Exception as e:
            print(f"✗ Error converting {md_file}: {str(e)}")
    
    print("\n" + "="*60)
    print("CONVERSION COMPLETE!")
    print("="*60)
    print("\nNote: For best results, use one of these alternatives:")
    print("1. Install Pandoc: https://pandoc.org/installing.html")
    print("2. Use VS Code with 'Markdown PDF' extension")
    print("3. Use online converter: https://www.markdowntopdf.com/")

def simple_html_export():
    """Export to HTML which can then be printed to PDF from browser"""
    try:
        from markdown2 import Markdown
    except ImportError:
        print("Installing markdown2...")
        os.system(f"{sys.executable} -m pip install markdown2")
        from markdown2 import Markdown
    
    docs_dir = os.path.dirname(os.path.abspath(__file__))
    
    files = [
        'USER_MANUAL.md',
        'TECHNICAL_DOCUMENTATION.md',
        'DEVELOPER_GUIDE.md'
    ]
    
    markdowner = Markdown(extras=["tables", "fenced-code-blocks", "code-friendly"])
    
    # HTML template with nice styling
    html_template = """<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{title}</title>
    <style>
        body {{
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            color: #333;
        }}
        h1 {{ color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }}
        h2 {{ color: #34495e; border-bottom: 2px solid #ecf0f1; padding-bottom: 8px; margin-top: 30px; }}
        h3 {{ color: #34495e; margin-top: 25px; }}
        code {{
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }}
        pre {{
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }}
        pre code {{
            background: none;
            color: #ecf0f1;
        }}
        table {{
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }}
        th, td {{
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }}
        th {{
            background-color: #3498db;
            color: white;
        }}
        tr:nth-child(even) {{
            background-color: #f9f9f9;
        }}
        blockquote {{
            border-left: 4px solid #3498db;
            padding-left: 20px;
            margin-left: 0;
            color: #666;
            font-style: italic;
        }}
        a {{ color: #3498db; text-decoration: none; }}
        a:hover {{ text-decoration: underline; }}
        
        @media print {{
            body {{ padding: 20px; }}
            h1, h2, h3 {{ page-break-after: avoid; }}
            pre, blockquote {{ page-break-inside: avoid; }}
        }}
    </style>
</head>
<body>
{content}
</body>
</html>"""
    
    for md_file in files:
        md_path = os.path.join(docs_dir, md_file)
        html_path = os.path.join(docs_dir, md_file.replace('.md', '.html'))
        
        if not os.path.exists(md_path):
            print(f"✗ File not found: {md_file}")
            continue
        
        print(f"Converting {md_file} to HTML...")
        
        try:
            with open(md_path, 'r', encoding='utf-8') as f:
                md_content = f.read()
            
            html_content = markdowner.convert(md_content)
            title = md_file.replace('.md', '').replace('_', ' ')
            
            final_html = html_template.format(title=title, content=html_content)
            
            with open(html_path, 'w', encoding='utf-8') as f:
                f.write(final_html)
            
            print(f"✓ Created: {html_path}")
            
        except Exception as e:
            print(f"✗ Error converting {md_file}: {str(e)}")
    
    print("\n" + "="*60)
    print("HTML EXPORT COMPLETE!")
    print("="*60)
    print("\nTo create PDFs:")
    print("1. Open each .html file in your browser")
    print("2. Press Ctrl+P (Print)")
    print("3. Select 'Save as PDF'")
    print("4. Save the PDF file")

if __name__ == "__main__":
    print("="*60)
    print("Tequila App Documentation Converter")
    print("="*60)
    print("\nChoose conversion method:")
    print("1. Export to HTML (recommended - easy to print to PDF)")
    print("2. Direct PDF conversion (requires additional setup)")
    
    choice = input("\nEnter choice (1 or 2): ").strip()
    
    if choice == "1":
        simple_html_export()
    elif choice == "2":
        convert_with_markdown2pdf()
    else:
        print("Invalid choice. Defaulting to HTML export...")
        simple_html_export()
