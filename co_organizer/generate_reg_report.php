<?php
require '../vendor/autoload.php';
require '../vendor/setasign/fpdf/fpdf.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'co-organizer') {
    header("Location: ../log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$participantsCollection = $db->participants;
// Fetch college name from interface collection
$interfaceCollection = $db->interface;
$home = $interfaceCollection->findOne(['page' => 'home']);
$collegeName = $home['college_name']['value'];


// Validate event ID
if (!isset($_POST['event_id']) || !preg_match('/^[a-f\d]{24}$/i', $_POST['event_id'])) {
    die("Invalid Event ID");
}

$eventId = new ObjectId($_POST['event_id']);
$event = $db->events->findOne(['_id' => $eventId]);

if (!$event) {
    die("Event not found");
}




// Get current timestamp
$timestamp = date('d-m-Y h:i A');

$participants = $participantsCollection->find([
    'event_id' => $eventId,
    'status' => 'approved'
])->toArray();

class PDF extends FPDF
{
    protected $widths;
    protected $aligns;
    public $collegeName;
    public $timestamp;

    function setCollegeName($collegeName) {
        $this->collegeName = $collegeName;
    }

    function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    function Header()
    {
        global $event;
        if ($event) {
            // Add some margin at the top
            $this->SetY(10);
            
            if (empty($this->collegeName)) {
                $client = new Client("mongodb://localhost:27017");
                $db = $client->campusconnect;
                $interfaceCollection = $db->interface;
                $home = $interfaceCollection->findOne(['page' => 'home']);
                $collegeName = $home['college_name']['value'];
                $this->collegeName = $collegeName;
            }
            
            if (empty($this->timestamp)) {
                $this->timestamp = date('d-m-Y h:i A');
            }
            
            // Draw a border around the header for visibility
            $startY = $this->GetY();
            
            $CoorganizerCollection = $db->co_organizers;
            $Coorganizer = $CoorganizerCollection->findOne(['username' => $_SESSION['username']]);
            $Coorganizername = $Coorganizer['name'];
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, $this->collegeName, 0, 1, 'C');
            
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Event: ' . html_entity_decode($event['event_name']), 0, 1, 'C');
            $this->Cell(0, 10, 'Co-Organizer: ' . $Coorganizername, 0, 1, 'C');
            
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 10, 'Generated on: ' . $this->timestamp, 0, 1, 'C');
            
            // Underline after the header to separate it from content
            $this->Line(10, $this->GetY(), $this->GetPageWidth() - 10, $this->GetY());
            
            $this->Ln(5);
        }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    function Row($data)
    {
        // Calculate the height of the row
        $nb = 0;
        for($i=0; $i<count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = 6*$nb;

        // Issue a page break first if needed
        $this->CheckPageBreak($h);
        
        // Draw the cells of the row
        for($i=0; $i<count($data); $i++)
        {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            
            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            
            // Draw the border
            $this->Rect($x, $y, $w, $h);
            
            // Print the text
            $this->MultiCell($w, 6, $data[$i], 0, $a);
            
            // Put the position to the right of the cell
            $this->SetXY($x+$w, $y);
        }
        
        // Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        // If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        // Compute the number of lines a MultiCell of width w will take
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
            
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        
        while($i<$nb)
        {
            $c = $s[$i];
            if($c=="\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            
            if($c==' ')
                $sep = $i;
                
            $l += $this->GetStringWidth(substr($s, $i, 1));
            
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i = $sep+1;
                    
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function ParticipantTable($header, $data)
    {
        // Calculate total width of the table
        $tableWidth = 0;
        $columnWidths = [40, 50, 75, 35, 35]; // Adjusted college name column to be wider
        
        foreach($columnWidths as $width) {
            $tableWidth += $width;
        }
        
        // Center the table by setting left margin
        $pageWidth = $this->GetPageWidth();
        $leftMargin = ($pageWidth - $tableWidth) / 2;
        $this->SetLeftMargin($leftMargin);
        $this->SetX($leftMargin);
        
        // Set column widths
        $this->SetWidths($columnWidths);
        
        // Set column alignments
        $this->SetAligns(['L', 'L', 'L', 'L', 'L']);
        
        // Print header
        $this->SetFont('Arial', 'B', 10);
        $this->Row($header);
        
        // Print rows
        $this->SetFont('Arial', '', 10);
        foreach($data as $row) {
            $this->Row($row);
        }
    }
}

// Start output buffering to catch any unwanted output 
ob_start();

// Create PDF object
$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage('L'); // Landscape orientation for better fit

// Explicitly set the college name and timestamp
$pdf->setCollegeName($collegeName);
$pdf->setTimestamp($timestamp);

// Debug: Check values are set correctly
// Comment this out for production use
/*
if (empty($pdf->collegeName) || empty($pdf->timestamp)) {
    die("Error: College name or timestamp not set correctly.");
}
*/

// Process the college names to ensure they don't overflow
$processedData = [];
foreach ($participants as $participant) {
    // Make sure college name fits in the cell by adding line breaks if needed
    $participantCollegeName = html_entity_decode($participant['college_name']);
    // Add a line break if it's too long
    if (strlen($participantCollegeName) > 40) {
        $words = explode(' ', $participantCollegeName);
        $newCollegeName = '';
        $lineLength = 0;
        
        foreach ($words as $word) {
            if ($lineLength + strlen($word) > 30) {
                $newCollegeName .= "\n" . $word . ' ';
                $lineLength = strlen($word) + 1;
            } else {
                $newCollegeName .= $word . ' ';
                $lineLength += strlen($word) + 1;
            }
        }
        $participantCollegeName = trim($newCollegeName);
    }
    
    $processedData[] = [
        html_entity_decode($participant['name']),
        html_entity_decode($participant['email']),
        $participantCollegeName,
        html_entity_decode($participant['phone']),
        '' // Empty remarks column
    ];
}

// No participants error handling
if (count($processedData) === 0) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'No approved participants found for this event.', 0, 1, 'C');
} else {
    $header = ['Name', 'Email', 'College Name', 'Phone Number', 'Remarks'];
    $pdf->ParticipantTable($header, $processedData);
}

// Clear any unwanted output
ob_end_clean();

// Output the PDF
$pdf->Output('D', 'registrations_report.pdf');
?>